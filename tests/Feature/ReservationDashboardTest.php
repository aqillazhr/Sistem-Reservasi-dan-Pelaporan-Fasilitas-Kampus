<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\Location;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test halaman "Halaman Reservasi" (hub, hitung status) sisi pengguna dan
 * "Kelola Reservasi" (tab filter + pencarian) sisi petugas.
 */
class ReservationDashboardTest extends TestCase
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

        $this->user = $this->makeUser('pengguna', 'Budi');
        $this->officer = $this->makeUser('petugas', 'Petugas Satu');
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

    private function makeUser(string $role, string $name): User
    {
        return User::create([
            'name' => $name, 'email' => strtolower($name).uniqid().'@kampus.ac.id',
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

    public function test_hub_menghitung_setiap_status_dengan_benar(): void
    {
        $this->makeReservation(['status' => 'pending', 'start_time' => '07:00', 'end_time' => '07:30']);
        $this->makeReservation(['status' => 'pending', 'start_time' => '07:30', 'end_time' => '08:00']);
        $this->makeReservation(['status' => 'rejected', 'start_time' => '08:00', 'end_time' => '08:30']);
        $this->makeReservation(['status' => 'cancelled', 'start_time' => '08:30', 'end_time' => '09:00']);
        // approved & akan datang (25 Sep) -> 'aktif'
        $this->makeReservation(['status' => 'approved', 'reservation_date' => '2026-09-25', 'start_time' => '09:00', 'end_time' => '10:00']);
        // approved & sudah lewat (23 Sep) -> 'selesai'
        $this->makeReservation(['status' => 'approved', 'reservation_date' => '2026-09-23', 'start_time' => '09:00', 'end_time' => '10:00']);

        $response = $this->actingAs($this->user)->get(route('pengguna.reservations.hub'))->assertOk();

        $response->assertViewHas('counts', function ($counts) {
            return $counts['pending'] === 2
                && $counts['rejected'] === 1
                && $counts['cancelled'] === 1
                && $counts['aktif'] === 1
                && $counts['selesai'] === 1;
        });
    }

    public function test_hub_tidak_menghitung_punya_pengguna_lain(): void
    {
        $other = $this->makeUser('pengguna', 'Lain');
        $this->makeReservation(['user_id' => $other->id]);

        $response = $this->actingAs($this->user)->get(route('pengguna.reservations.hub'))->assertOk();

        $response->assertViewHas('counts', fn ($counts) => $counts['pending'] === 0);
    }

    public function test_kelola_reservasi_tab_filter_menampilkan_status_yang_sesuai(): void
    {
        $this->makeReservation(['status' => 'pending']);
        // Aktif: approved, tanggalnya (25 Sep) belum lewat dari "sekarang" (24 Sep 08.00).
        $this->makeReservation(['status' => 'approved', 'start_time' => '11:00', 'end_time' => '12:00']);
        // Selesai: approved, tanggal+jamnya (23 Sep) sudah lewat.
        $this->makeReservation(['status' => 'approved', 'reservation_date' => '2026-09-23', 'start_time' => '11:00', 'end_time' => '12:00']);
        $this->makeReservation(['status' => 'rejected', 'start_time' => '13:00', 'end_time' => '14:00']);
        $this->makeReservation(['status' => 'cancelled', 'start_time' => '15:00', 'end_time' => '16:00']);

        $pending = $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index', ['status' => 'menunggu']))
            ->assertOk();
        $pending->assertViewHas('reservations', fn ($rows) => $rows->total() === 1);

        $aktif = $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index', ['status' => 'aktif']))
            ->assertOk();
        $aktif->assertViewHas('reservations', fn ($rows) => $rows->total() === 1);

        $selesai = $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index', ['status' => 'selesai']))
            ->assertOk();
        $selesai->assertViewHas('reservations', fn ($rows) => $rows->total() === 1);

        $semua = $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index', ['status' => 'semua']))
            ->assertOk();
        $semua->assertViewHas('reservations', fn ($rows) => $rows->total() === 5);
    }

    public function test_kelola_reservasi_pencarian_berdasarkan_nama_pengguna_dan_fasilitas(): void
    {
        $this->makeReservation(); // milik $this->user ("Budi"), fasilitas "Aula Utama"

        $byUser = $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index', ['status' => 'menunggu', 'q' => 'Budi']))
            ->assertOk();
        $byUser->assertViewHas('reservations', fn ($rows) => $rows->total() === 1);

        $byFacility = $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index', ['status' => 'menunggu', 'q' => 'Aula Utama']))
            ->assertOk();
        $byFacility->assertViewHas('reservations', fn ($rows) => $rows->total() === 1);

        $noMatch = $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index', ['status' => 'menunggu', 'q' => 'Tidak Ada Begini']))
            ->assertOk();
        $noMatch->assertViewHas('reservations', fn ($rows) => $rows->total() === 0);
    }

    public function test_kelola_reservasi_tab_tidak_valid_jatuh_ke_default_menunggu(): void
    {
        $this->makeReservation(['status' => 'pending']);
        $this->makeReservation(['status' => 'approved', 'start_time' => '11:00', 'end_time' => '12:00']);

        $response = $this->actingAs($this->officer)
            ->get(route('petugas.reservations.index', ['status' => 'tidak-valid']))
            ->assertOk();

        $response->assertViewHas('tab', 'menunggu');
        $response->assertViewHas('reservations', fn ($rows) => $rows->total() === 1);
    }
}
