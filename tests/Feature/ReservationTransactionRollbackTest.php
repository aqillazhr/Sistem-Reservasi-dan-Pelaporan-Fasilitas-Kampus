<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\Location;
use App\Models\Reservation;
use App\Models\StatusLog;
use App\Models\User;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use RuntimeException;
use Tests\TestCase;

/**
 * Bukti bahwa DB::transaction() di ReservationService BENAR-BENAR melakukan
 * rollback penuh, bukan cuma "tidak menulis apa-apa kalau gagal validasi di
 * langkah pertama". Setiap test di sini SENGAJA membuat langkah TERAKHIR di
 * dalam sebuah transaction gagal (lewat event Eloquent), lalu memastikan
 * langkah-langkah SEBELUMNYA di transaction yang sama (yang sudah sempat
 * ter-INSERT/UPDATE di dalam request yang sama) ikut dibatalkan juga.
 *
 * Kalau salah satu operasi tulis di ReservationService lupa dibungkus
 * DB::transaction (atau baris kritisnya taruh di luar closure), test di
 * bawah ini akan gagal karena data parsial akan tetap "nyangkut" di DB.
 */
class ReservationTransactionRollbackTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $officer;

    private Facility $facility;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 9, 24, 8, 0, 0, 'Asia/Jakarta'));

        User::create([
            'name' => 'Sistem', 'email' => config('reservation.system_account_email'),
            'password' => 'password', 'role' => 'admin', 'user_type' => null,
            'status' => 'verified', 'account_status' => 'aktif',
        ]);

        $this->user = $this->makeUser('pengguna');
        $this->officer = $this->makeUser('petugas');
        $location = Location::create(['scope_level' => 'universitas']);
        $type = FacilityType::create(['name' => 'Aula']);
        $this->facility = Facility::create([
            'name' => 'Aula Utama', 'type_id' => $type->id, 'location_id' => $location->id,
            'capacity' => 200, 'status' => 'aktif',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role), 'email' => "{$role}".uniqid().'@kampus.ac.id',
            'password' => 'password', 'role' => $role,
            'user_type' => $role === 'pengguna' ? 'mahasiswa' : null,
            'status' => 'verified', 'account_status' => 'aktif',
        ]);
    }

    private function makeReservation(array $o = []): Reservation
    {
        return Reservation::create(array_merge([
            'user_id' => $this->user->id, 'facility_id' => $this->facility->id,
            'reservation_date' => '2026-09-25', 'start_time' => '09:00', 'end_time' => '10:00',
            'purpose' => 'Kegiatan', 'status' => 'pending',
        ], $o));
    }

    public function test_create_rollback_penuh_kalau_penulisan_status_log_gagal(): void
    {
        // StatusLog ditulis SETELAH Reservation di dalam transaction yang sama
        // (lihat ReservationService::create()). Kalau langkah StatusLog gagal,
        // Reservation yang barusan ter-INSERT juga HARUS ikut hilang.
        Event::listen('eloquent.creating: '.StatusLog::class, function () {
            throw new RuntimeException('Simulasi gagal menulis status_logs.');
        });

        try {
            app(ReservationService::class)->create($this->user, [
                'facility_id' => $this->facility->id,
                'reservation_date' => '2026-09-25',
                'start_time' => '09:00',
                'end_time' => '10:00',
                'purpose' => 'Uji rollback penuh saat create',
            ]);
            $this->fail('Seharusnya melempar exception.');
        } catch (RuntimeException) {
            // sesuai skenario
        }

        $this->assertDatabaseCount('reservations', 0);
        $this->assertDatabaseCount('status_logs', 0);
    }

    public function test_approve_rollback_penuh_kalau_penulisan_status_log_gagal(): void
    {
        $r = $this->makeReservation();

        // update status ke 'approved' terjadi SEBELUM StatusLog::create() di
        // approveByOfficer(); kalau StatusLog gagal, update status itu juga
        // harus ikut di-rollback (reservasi tidak boleh "nyangkut" approved
        // tanpa audit log).
        Event::listen('eloquent.creating: '.StatusLog::class, function () {
            throw new RuntimeException('Simulasi gagal menulis status_logs saat approve.');
        });

        try {
            app(ReservationService::class)->approveByOfficer($this->officer, $r->id);
            $this->fail('Seharusnya melempar exception.');
        } catch (RuntimeException) {
            // sesuai skenario
        }

        $this->assertSame('pending', $r->fresh()->status);
        $this->assertDatabaseMissing('status_logs', ['reservation_id' => $r->id]);
    }

    public function test_cancel_oleh_pengguna_rollback_penuh_kalau_penulisan_status_log_gagal(): void
    {
        $r = $this->makeReservation();

        Event::listen('eloquent.creating: '.StatusLog::class, function () {
            throw new RuntimeException('Simulasi gagal menulis status_logs saat cancel.');
        });

        try {
            app(ReservationService::class)->cancelByOwner($this->user, $r->id);
            $this->fail('Seharusnya melempar exception.');
        } catch (RuntimeException) {
            // sesuai skenario
        }

        $this->assertSame('pending', $r->fresh()->status);
        $this->assertDatabaseMissing('status_logs', ['reservation_id' => $r->id]);
    }

    public function test_reject_rollback_penuh_kalau_penulisan_status_log_gagal(): void
    {
        $r = $this->makeReservation();

        Event::listen('eloquent.creating: '.StatusLog::class, function () {
            throw new RuntimeException('Simulasi gagal menulis status_logs saat reject.');
        });

        try {
            app(ReservationService::class)->rejectByOfficer($this->officer, $r->id, 'Alasan penolakan uji rollback');
            $this->fail('Seharusnya melempar exception.');
        } catch (RuntimeException) {
            // sesuai skenario
        }

        $this->assertSame('pending', $r->fresh()->status);
        $this->assertDatabaseMissing('status_logs', ['reservation_id' => $r->id]);
    }

    public function test_konflik_saat_create_tidak_menyisakan_data_parsial(): void
    {
        // Sudah ada reservasi approved yang bentrok -> hasConflict() melempar
        // ValidationException DI DALAM transaction, SETELAH lock user & facility
        // diambil tapi SEBELUM INSERT reservations terjadi.
        $this->makeReservation(['status' => 'approved']);

        try {
            app(ReservationService::class)->create($this->user, [
                'facility_id' => $this->facility->id,
                'reservation_date' => '2026-09-25',
                'start_time' => '09:00',
                'end_time' => '10:00',
                'purpose' => 'Percobaan bentrok',
            ]);
            $this->fail('Seharusnya melempar ValidationException.');
        } catch (\Illuminate\Validation\ValidationException) {
            // sesuai skenario
        }

        // Hanya reservasi approved awal (dibuat langsung lewat Eloquent, tanpa
        // status_logs) yang ada; percobaan create() yang gagal tidak menyisakan
        // baris apapun di kedua tabel.
        $this->assertDatabaseCount('reservations', 1);
        $this->assertDatabaseCount('status_logs', 0);
    }

    public function test_max_pending_tercapai_saat_create_tidak_menulis_apapun(): void
    {
        $maxPending = (int) config('reservation.max_pending_per_user');

        foreach (range(0, $maxPending - 1) as $i) {
            $this->makeReservation(['start_time' => sprintf('%02d:00', 7 + $i), 'end_time' => sprintf('%02d:30', 7 + $i)]);
        }

        try {
            app(ReservationService::class)->create($this->user, [
                'facility_id' => $this->facility->id,
                'reservation_date' => '2026-09-25',
                'start_time' => '19:00',
                'end_time' => '19:30',
                'purpose' => 'Percobaan lewat kuota',
            ]);
            $this->fail('Seharusnya melempar ValidationException.');
        } catch (\Illuminate\Validation\ValidationException) {
            // sesuai skenario
        }

        // Reservasi seed dibuat langsung lewat Eloquent (makeReservation()), bukan
        // lewat service, jadi tidak ada status_logs untuknya. Percobaan create()
        // yang gagal juga tidak boleh menambah baris apapun di kedua tabel.
        $this->assertDatabaseCount('reservations', $maxPending);
        $this->assertDatabaseCount('status_logs', 0);
    }
}
