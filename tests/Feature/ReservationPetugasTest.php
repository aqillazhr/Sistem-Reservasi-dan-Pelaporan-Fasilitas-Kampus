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
use Tests\TestCase;

/**
 * Test sisi petugas (Orang 3): approve/reject/batal darurat, dan
 * auto-cancel reservasi pending yang sudah lewat waktu booking-nya.
 * Jalankan di MySQL (migration pakai CHECK constraint), sama seperti
 * ReservationPenggunaTest.
 */
class ReservationPetugasTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private User $pengguna;
    private Facility $facility;

    protected function setUp(): void
    {
        parent::setUp();

        // Kamis 24 Sep 2026, 08.00 WIB
        Carbon::setTestNow(Carbon::create(2026, 9, 24, 8, 0, 0, 'Asia/Jakarta'));

        // Akun Sistem wajib ada (dipakai autoCancelExpired sebagai pelaku log).
        User::create([
            'name' => 'Sistem', 'email' => config('reservation.system_account_email'),
            'password' => 'password', 'role' => 'admin', 'user_type' => null,
            'status' => 'verified', 'account_status' => 'aktif',
        ]);

        $this->officer = $this->makeUser('petugas');
        $this->pengguna = $this->makeUser('pengguna');
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

    private function makeUser(string $role, ?string $email = null): User
    {
        return User::create([
            'name' => ucfirst($role), 'email' => $email ?? "{$role}".uniqid().'@kampus.ac.id',
            'password' => 'password', 'role' => $role,
            'user_type' => $role === 'pengguna' ? 'mahasiswa' : null,
            'status' => 'verified', 'account_status' => 'aktif',
        ]);
    }

    private function makeReservation(array $o = []): Reservation
    {
        return Reservation::create(array_merge([
            'user_id' => $this->pengguna->id, 'facility_id' => $this->facility->id,
            'reservation_date' => '2026-09-25', 'start_time' => '09:00', 'end_time' => '10:00',
            'purpose' => 'Kegiatan', 'status' => 'pending',
        ], $o));
    }

    // ==================================================================
    // Approve / Reject / Batal darurat — transaction + lock + audit log
    // ==================================================================

    public function test_petugas_bisa_approve_reservasi_pending(): void
    {
        $r = $this->makeReservation();

        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.approve', $r))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame('approved', $r->fresh()->status);
        $this->assertDatabaseHas('status_logs', [
            'reservation_id' => $r->id, 'old_status' => 'pending', 'new_status' => 'approved',
            'changed_by_user_id' => $this->officer->id,
        ]);
    }

    public function test_approve_ditolak_kalau_reservasi_sudah_diproses_sebelumnya(): void
    {
        $r = $this->makeReservation(['status' => 'rejected']);

        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.approve', $r))
            ->assertSessionHasErrors('reservation');

        $this->assertSame('rejected', $r->fresh()->status); // tidak berubah
    }

    public function test_approve_ditolak_kalau_bentrok_dengan_yang_sudah_approved(): void
    {
        // Skenario data yang masuk di luar jalur normal (mis. lewat Tinker/seeder),
        // sehingga dua baris bentrok bisa ada sekaligus di database.
        $this->makeReservation(['start_time' => '09:00', 'end_time' => '10:00', 'status' => 'approved']);
        $pending = $this->makeReservation(['start_time' => '09:30', 'end_time' => '10:30', 'status' => 'pending']);

        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.approve', $pending))
            ->assertSessionHasErrors('reservation');

        $this->assertSame('pending', $pending->fresh()->status); // tidak ikut berubah
        $this->assertDatabaseMissing('status_logs', ['reservation_id' => $pending->id]);
    }

    public function test_petugas_menolak_wajib_isi_alasan_dan_slot_kembali_tersedia(): void
    {
        $r = $this->makeReservation();

        // Tanpa alasan -> gagal validasi, status tidak berubah.
        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.reject', $r), [])
            ->assertSessionHasErrors('note');
        $this->assertSame('pending', $r->fresh()->status);

        // Dengan alasan -> berhasil, tercatat di status_logs.note (BUKAN cancellation_reason).
        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.reject', $r), ['note' => 'Fasilitas dipakai acara lain yang lebih prioritas'])
            ->assertSessionHasNoErrors();

        $r->refresh();
        $this->assertSame('rejected', $r->status);
        $this->assertNull($r->cancellation_reason);
        $this->assertDatabaseHas('status_logs', [
            'reservation_id' => $r->id, 'new_status' => 'rejected',
            'note' => 'Fasilitas dipakai acara lain yang lebih prioritas',
        ]);

        // Slot yang sama sekarang harus bisa diajukan pengguna lain.
        $other = $this->makeUser('pengguna');
        $this->actingAs($other)
            ->post(route('pengguna.reservations.store'), [
                'facility_id' => $this->facility->id, 'reservation_date' => '2026-09-25',
                'start_time' => '09:00', 'end_time' => '10:00', 'purpose' => 'Rapat lain',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_petugas_batalkan_hanya_untuk_status_approved(): void
    {
        $pending = $this->makeReservation();

        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.petugas-cancel', $pending), ['cancellation_reason' => 'Darurat'])
            ->assertSessionHasErrors('reservation');
        $this->assertSame('pending', $pending->fresh()->status);

        $approved = $this->makeReservation(['start_time' => '11:00', 'end_time' => '12:00', 'status' => 'approved']);

        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.petugas-cancel', $approved), [])
            ->assertSessionHasErrors('cancellation_reason'); // wajib isi alasan

        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.petugas-cancel', $approved), ['cancellation_reason' => 'AC rusak mendadak'])
            ->assertSessionHasNoErrors();

        $approved->refresh();
        $this->assertSame('cancelled', $approved->status);
        $this->assertSame('AC rusak mendadak', $approved->cancellation_reason);
        $this->assertDatabaseHas('status_logs', [
            'reservation_id' => $approved->id, 'old_status' => 'approved', 'new_status' => 'cancelled',
            'changed_by_user_id' => $this->officer->id,
        ]);
    }

    public function test_pengguna_tidak_bisa_akses_route_petugas(): void
    {
        $r = $this->makeReservation();

        $this->actingAs($this->pengguna)
            ->post(route('petugas.reservations.approve', $r))
            ->assertForbidden();
    }

    // ==================================================================
    // Detail reservasi sisi petugas (baris tabel Kelola Reservasi bisa diklik).
    // ==================================================================

    public function test_petugas_bisa_lihat_detail_reservasi(): void
    {
        $r = $this->makeReservation(['purpose' => 'Tujuan detail yang cukup panjang untuk diuji']);

        $this->actingAs($this->officer)
            ->get(route('petugas.reservations.show', $r))
            ->assertOk()
            ->assertSee('Tujuan detail yang cukup panjang untuk diuji')
            ->assertSee($this->pengguna->name);
    }

    public function test_pengguna_tidak_bisa_akses_detail_reservasi_petugas(): void
    {
        $r = $this->makeReservation();

        $this->actingAs($this->pengguna)
            ->get(route('petugas.reservations.show', $r))
            ->assertForbidden();
    }

    // ==================================================================
    // Auto-cancel reservasi pending yang lewat waktu booking (bukan cron —
    // dieksekusi "on the fly" di titik baca yang relevan).
    // ==================================================================

    public function test_pending_yang_lewat_waktu_booking_otomatis_jadi_cancelled(): void
    {
        // Booking 12.00-12.30 tanggal 24 (hari ini), sekarang sudah jam 13.00 tanggal 24.
        $r = $this->makeReservation(['reservation_date' => '2026-09-24', 'start_time' => '12:00', 'end_time' => '12:30']);
        Carbon::setTestNow(Carbon::create(2026, 9, 24, 13, 0, 0, 'Asia/Jakarta'));

        app(ReservationService::class)->autoCancelExpired();

        $r->refresh();
        $this->assertSame('cancelled', $r->status);
        $this->assertStringContainsString('otomatis oleh sistem', $r->cancellation_reason);

        $systemId = User::where('email', config('reservation.system_account_email'))->value('id');
        $this->assertDatabaseHas('status_logs', [
            'reservation_id' => $r->id, 'old_status' => 'pending', 'new_status' => 'cancelled',
            'changed_by_user_id' => $systemId,
        ]);
    }

    public function test_pending_yang_masih_berjalan_tidak_ikut_dibatalkan(): void
    {
        // Booking 12.00-13.30 tanggal 24, sekarang jam 13.00 (masih berjalan, belum end_time).
        $r = $this->makeReservation(['reservation_date' => '2026-09-24', 'start_time' => '12:00', 'end_time' => '13:30']);
        Carbon::setTestNow(Carbon::create(2026, 9, 24, 13, 0, 0, 'Asia/Jakarta'));

        app(ReservationService::class)->autoCancelExpired();

        $this->assertSame('pending', $r->fresh()->status);
    }

    public function test_auto_cancel_tidak_menyentuh_approved_atau_reservasi_pengguna_lain(): void
    {
        $other = $this->makeUser('pengguna');
        $approvedExpired = $this->makeReservation([
            'reservation_date' => '2026-09-23', 'start_time' => '09:00', 'end_time' => '10:00', 'status' => 'approved',
        ]);
        $pendingOther = $this->makeReservation([
            'user_id' => $other->id, 'reservation_date' => '2026-09-23', 'start_time' => '09:00', 'end_time' => '10:00',
        ]);
        $pendingMine = $this->makeReservation([
            'reservation_date' => '2026-09-23', 'start_time' => '11:00', 'end_time' => '12:00',
        ]);

        // Sweep khusus milik $this->pengguna saja (dipakai ReservationService::create()).
        app(ReservationService::class)->autoCancelExpired($this->pengguna->id);

        $this->assertSame('approved', $approvedExpired->fresh()->status); // bukan pending, tidak disentuh
        $this->assertSame('pending', $pendingOther->fresh()->status);    // milik user lain, tidak ikut ter-sweep
        $this->assertSame('cancelled', $pendingMine->fresh()->status);   // ini yang harus berubah
    }

    public function test_kelola_reservasi_petugas_menjalankan_auto_cancel_sebelum_menampilkan_antrian(): void
    {
        $r = $this->makeReservation(['reservation_date' => '2026-09-23', 'start_time' => '09:00', 'end_time' => '10:00']);

        $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index'))
            ->assertOk();

        $this->assertSame('cancelled', $r->fresh()->status);
    }

    public function test_reservasi_kedaluwarsa_tidak_bisa_di_approve_lagi_kalau_kelewatan_disweep(): void
    {
        // Skenario: autoCancelExpired belum sempat jalan (mis. race), petugas
        // klik approve duluan tepat setelah lewat waktu -> tetap harus ditolak.
        $r = $this->makeReservation(['reservation_date' => '2026-09-24', 'start_time' => '07:00', 'end_time' => '07:30']);
        Carbon::setTestNow(Carbon::create(2026, 9, 24, 8, 0, 1, 'Asia/Jakarta')); // 1 detik setelah end_time

        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.approve', $r))
            ->assertSessionHasErrors('reservation');

        $this->assertSame('pending', $r->fresh()->status); // approve gagal, tapi juga BELUM auto-cancelled di sini
    }

    public function test_reservasi_kedaluwarsa_tidak_bisa_ditolak_lagi_kalau_kelewatan_disweep(): void
    {
        // Simetris dengan approve: reservasi yang waktu booking-nya sudah lewat
        // juga tidak boleh ditolak manual lagi (tetap harus lewat autoCancelExpired()).
        $r = $this->makeReservation(['reservation_date' => '2026-09-24', 'start_time' => '07:00', 'end_time' => '07:30']);
        Carbon::setTestNow(Carbon::create(2026, 9, 24, 8, 0, 1, 'Asia/Jakarta')); // 1 detik setelah end_time

        $this->actingAs($this->officer)
            ->post(route('petugas.reservations.reject', $r), ['note' => 'Terlambat diproses'])
            ->assertSessionHasErrors('reservation');

        $this->assertSame('pending', $r->fresh()->status); // reject gagal, tapi juga BELUM auto-cancelled di sini
    }
}
