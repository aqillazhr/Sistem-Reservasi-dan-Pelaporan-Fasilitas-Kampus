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
 * Test akses/sesi untuk modul reservasi:
 * - Semua route reservasi (pengguna & petugas) wajib login.
 * - Akun yang baru dinonaktifkan/belum diverifikasi dipaksa logout oleh
 *   EnsureUserHasRole meski sesi lamanya masih ada di browser (mencegah
 *   akun nonaktif tetap bisa transaksi selama sesi belum expired).
 * - Login meregenerasi ID sesi (proteksi session fixation).
 * - Endpoint publik (slot fasilitas) tidak membocorkan status login/sesi.
 */
class ReservationSecurityTest extends TestCase
{
    use RefreshDatabase;

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

    private function makeUser(string $role, array $override = []): User
    {
        return User::create(array_merge([
            'name' => ucfirst($role), 'email' => "{$role}".uniqid().'@kampus.ac.id',
            'password' => 'password', 'role' => $role,
            'user_type' => $role === 'pengguna' ? 'mahasiswa' : null,
            'status' => 'verified', 'account_status' => 'aktif',
        ], $override));
    }

    public function test_semua_route_reservasi_wajib_login(): void
    {
        $this->get(route('pengguna.reservations.hub'))->assertRedirect(route('login'));
        $this->get(route('pengguna.reservations.index'))->assertRedirect(route('login'));
        $this->get(route('pengguna.reservations.create'))->assertRedirect(route('login'));
        $this->post(route('pengguna.reservations.store'), [])->assertRedirect(route('login'));
        $this->get(route('petugas.reservations.index'))->assertRedirect(route('login'));
    }

    public function test_akun_yang_baru_dinonaktifkan_dipaksa_logout_walau_sesi_lama_masih_ada(): void
    {
        $user = $this->makeUser('pengguna');

        $this->actingAs($user)->get(route('pengguna.reservations.hub'))->assertOk();

        // Dinonaktifkan admin SETELAH pengguna login; sesi browser lama belum expired.
        $user->update(['account_status' => 'nonaktif']);

        $this->actingAs($user)
            ->get(route('pengguna.reservations.hub'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_akun_yang_belum_diverifikasi_tidak_bisa_akses_reservasi(): void
    {
        $user = $this->makeUser('pengguna', ['status' => 'pending', 'account_status' => null]);

        $this->actingAs($user)
            ->get(route('pengguna.reservations.hub'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_petugas_yang_dinonaktifkan_tidak_bisa_lagi_kelola_reservasi(): void
    {
        $officer = $this->makeUser('petugas');
        $r = Reservation::create([
            'user_id' => $this->makeUser('pengguna')->id, 'facility_id' => $this->facility->id,
            'reservation_date' => '2026-09-25', 'start_time' => '09:00', 'end_time' => '10:00',
            'purpose' => 'Rapat', 'status' => 'pending',
        ]);

        $officer->update(['account_status' => 'nonaktif']);

        $this->actingAs($officer)
            ->post(route('petugas.reservations.approve', $r))
            ->assertRedirect(route('login'));

        $this->assertSame('pending', $r->fresh()->status);
    }

    public function test_pengguna_tidak_bisa_lihat_reservasi_orang_lain_lewat_url_langsung(): void
    {
        $owner = $this->makeUser('pengguna');
        $intruder = $this->makeUser('pengguna');

        $r = Reservation::create([
            'user_id' => $owner->id, 'facility_id' => $this->facility->id,
            'reservation_date' => '2026-09-25', 'start_time' => '09:00', 'end_time' => '10:00',
            'purpose' => 'Rapat rahasia', 'status' => 'pending',
        ]);

        $this->actingAs($intruder)->get(route('pengguna.reservations.show', $r))->assertForbidden();
    }

    public function test_login_meregenerasi_id_sesi_untuk_cegah_session_fixation(): void
    {
        $user = $this->makeUser('pengguna');

        $this->get(route('login'));
        $oldSessionId = session()->getId();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('pengguna.dashboard'));

        $this->assertNotSame($oldSessionId, session()->getId());
    }

    public function test_logout_menginvalidasi_sesi_dan_reservasi_tidak_bisa_diakses_lagi(): void
    {
        $user = $this->makeUser('pengguna');

        $this->actingAs($user)->get(route('pengguna.reservations.hub'))->assertOk();

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->get(route('pengguna.reservations.hub'))->assertRedirect(route('login'));
    }

    public function test_endpoint_slot_publik_bisa_diakses_tanpa_login_dan_tidak_bocorkan_status_sesi(): void
    {
        $this->getJson(route('facilities.slots', $this->facility))
            ->assertOk()
            ->assertJsonStructure(['facility_id', 'from', 'days', 'slots']);

        $this->assertGuest();
    }
}
