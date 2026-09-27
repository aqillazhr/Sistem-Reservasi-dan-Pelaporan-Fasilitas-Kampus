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
 * Test sisi pengguna (Orang 3). Jalankan di MySQL (migration memakai CHECK constraint
 * MySQL): buat database ppk_reservasi_fasilitas_test lalu `php artisan test`.
 * Race condition sungguhan (dua request bersamaan) tetap dites manual dengan 2 browser.
 */
class ReservationPenggunaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Facility $facility;

    protected function setUp(): void
    {
        parent::setUp();

        // Kamis 24 Sep 2026, 08.00 WIB
        Carbon::setTestNow(Carbon::create(2026, 9, 24, 8, 0, 0, 'Asia/Jakarta'));

        $this->user = $this->makeUser('pengguna');
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

    private function payload(array $override = []): array
    {
        return array_merge([
            'facility_id' => $this->facility->id,
            'reservation_date' => '2026-09-25',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'purpose' => 'Rapat himpunan mahasiswa',
        ], $override);
    }

    private function book(array $override = [], ?User $as = null)
    {
        return $this->actingAs($as ?? $this->user)->post(route('pengguna.reservations.store'), $this->payload($override));
    }

    private function makeReservation(array $o = []): Reservation
    {
        return Reservation::create(array_merge([
            'user_id' => $this->user->id, 'facility_id' => $this->facility->id,
            'reservation_date' => '2026-09-25', 'start_time' => '09:00', 'end_time' => '10:00',
            'purpose' => 'Kegiatan', 'status' => 'pending',
        ], $o));
    }

    public function test_pengguna_bisa_mengajukan_reservasi_dan_status_pending(): void
    {
        $this->book()->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('reservations', ['user_id' => $this->user->id, 'status' => 'pending', 'start_time' => '09:00:00']);
        $this->assertDatabaseHas('status_logs', ['new_status' => 'pending', 'changed_by_user_id' => $this->user->id]);
    }

    public function test_di_luar_jam_operasional_ditolak(): void
    {
        $this->book(['start_time' => '06:30', 'end_time' => '07:30'])->assertSessionHasErrors('start_time');
        $this->book(['start_time' => '19:30', 'end_time' => '20:30'])->assertSessionHasErrors('start_time');
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_tepat_di_batas_jam_operasional_diterima(): void
    {
        $this->book(['reservation_date' => '2026-09-26', 'start_time' => '07:00', 'end_time' => '07:30'])->assertSessionHasNoErrors();
        $this->book(['reservation_date' => '2026-09-26', 'start_time' => '19:30', 'end_time' => '20:00'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('reservations', 2);
    }

    public function test_bukan_kelipatan_30_menit_ditolak(): void
    {
        $this->book(['start_time' => '09:15', 'end_time' => '10:00'])->assertSessionHasErrors('start_time');
        $this->book(['start_time' => '09:00', 'end_time' => '10:10'])->assertSessionHasErrors('end_time');
    }

    public function test_slot_bentrok_dengan_pending_ditolak_tapi_bersebelahan_boleh(): void
    {
        $this->makeReservation(['user_id' => $this->makeUser('pengguna')->id]);

        $this->book(['start_time' => '09:30', 'end_time' => '10:30'])->assertSessionHasErrors('start_time');
        $this->book(['start_time' => '10:00', 'end_time' => '11:00'])->assertSessionHasNoErrors(); // menempel di ujung
        $this->book(['start_time' => '08:00', 'end_time' => '09:00'])->assertSessionHasNoErrors();
    }

    public function test_slot_rejected_atau_cancelled_kembali_tersedia(): void
    {
        $this->makeReservation(['user_id' => $this->makeUser('pengguna')->id, 'status' => 'rejected']);

        $this->book()->assertSessionHasNoErrors();
    }

    public function test_fasilitas_nonaktif_atau_perbaikan_ditolak(): void
    {
        $this->facility->update(['status' => 'dalam perbaikan']);

        $this->book()->assertSessionHasErrors('facility_id');
    }

    public function test_tanggal_lewat_atau_lebih_dari_7_hari_ditolak(): void
    {
        $this->book(['reservation_date' => '2026-09-23'])->assertSessionHasErrors('reservation_date');
        $this->book(['reservation_date' => '2026-10-02'])->assertSessionHasErrors('reservation_date'); // H+8
        $this->book(['reservation_date' => '2026-10-01'])->assertSessionHasNoErrors();                   // H+7
    }

    public function test_jam_mulai_hari_ini_yang_sudah_lewat_ditolak(): void
    {
        $this->book(['reservation_date' => '2026-09-24', 'start_time' => '07:30', 'end_time' => '08:30'])
            ->assertSessionHasErrors('start_time');
    }

    public function test_maksimal_10_pending_per_pengguna(): void
    {
        foreach (range(0, 9) as $i) {
            $this->makeReservation(['start_time' => sprintf('%02d:00', 7 + $i), 'end_time' => sprintf('%02d:30', 7 + $i)]);
        }

        $this->book(['start_time' => '18:00', 'end_time' => '19:00'])->assertSessionHasErrors('facility_id');
    }

    public function test_batal_diizinkan_sebelum_h_minus_1_dan_menulis_log(): void
    {
        $r = $this->makeReservation(); // mulai 25 Sep 09.00 -> batas 24 Sep 09.00, sekarang 08.00

        $this->actingAs($this->user)->post(route('pengguna.reservations.cancel', $r))->assertSessionHasNoErrors();

        $this->assertSame('cancelled', $r->fresh()->status);
        $this->assertDatabaseHas('status_logs', ['reservation_id' => $r->id, 'old_status' => 'pending', 'new_status' => 'cancelled']);
    }

    public function test_batal_setelah_batas_ditolak_server_meski_tombol_dipaksa(): void
    {
        $r = $this->makeReservation(['status' => 'approved']);
        Carbon::setTestNow(Carbon::create(2026, 9, 24, 9, 0, 1, 'Asia/Jakarta'));

        $this->actingAs($this->user)->post(route('pengguna.reservations.cancel', $r))->assertSessionHasErrors('reservation');
        $this->assertSame('approved', $r->fresh()->status);
    }

    public function test_tidak_bisa_batal_reservasi_orang_lain_atau_yang_sudah_ditolak(): void
    {
        $other = $this->makeUser('pengguna');
        $mine = $this->makeReservation(['status' => 'rejected']);
        $theirs = $this->makeReservation(['user_id' => $other->id, 'start_time' => '12:00', 'end_time' => '13:00']);

        $this->actingAs($this->user)->post(route('pengguna.reservations.cancel', $theirs))->assertForbidden();
        $this->actingAs($this->user)->post(route('pengguna.reservations.cancel', $mine))->assertSessionHasErrors('reservation');
        $this->actingAs($this->user)->get(route('pengguna.reservations.show', $theirs))->assertForbidden();
    }

    public function test_petugas_tidak_bisa_memakai_route_pengguna(): void
    {
        $this->book([], $this->makeUser('petugas'))->assertForbidden();
    }

    public function test_endpoint_slot_publik_tidak_membocorkan_pemohon_dan_tujuan(): void
    {
        $this->makeReservation(['purpose' => 'RAHASIA-TUJUAN']);

        $res = $this->getJson(route('facilities.slots', $this->facility).'?from=2026-09-25&days=1')->assertOk();

        $res->assertJsonPath('slots.0.state', 'pending')->assertJsonPath('slots.0.start_time', '09:00');
        $this->assertStringNotContainsString('RAHASIA-TUJUAN', $res->getContent());
        $this->assertStringNotContainsString('user_id', $res->getContent());
    }

    // ==================================================================
    // REVISI 2: reservasi baru minimal H-1 (min_advance_hours) sebelum jam
    // mulai — sama seperti batas pembatalan. "Sekarang" di test ini selalu
    // 2026-09-24 08:00 (lihat setUp), jadi 2026-09-25 08:00 persis 24 jam
    // dari sekarang dan 2026-09-25 07:00 hanya 23 jam dari sekarang.
    // ==================================================================

    public function test_kurang_dari_h_min_1_dari_sekarang_ditolak(): void
    {
        $this->book(['reservation_date' => '2026-09-25', 'start_time' => '07:00', 'end_time' => '08:00'])
            ->assertSessionHasErrors('start_time');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_tepat_di_batas_h_min_1_diterima(): void
    {
        $this->book(['reservation_date' => '2026-09-25', 'start_time' => '08:00', 'end_time' => '09:00'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_papan_slot_menandai_state_toosoon_sebelum_batas_h_min_1(): void
    {
        $board = app(\App\Services\ReservationService::class)->slotBoard($this->facility->id, '2026-09-25');
        $byStart = collect($board)->keyBy('start');

        $this->assertSame('toosoon', $byStart['07:00']['state']); // 23 jam dari sekarang
        $this->assertSame('free', $byStart['09:00']['state']);    // 25 jam dari sekarang
    }

    // ==================================================================
    // REVISI 3: reservasi pending yang jam mulainya sudah lewat tapi belum
    // diproses petugas otomatis dianggap "Kedaluwarsa" (label saja, bukan
    // status baru di DB) DAN tidak lagi mengunci kuota max_pending_per_user.
    // ==================================================================

    public function test_pending_yang_sudah_lewat_waktunya_berlabel_kedaluwarsa(): void
    {
        // Dibuat "kemarin" untuk slot yang sudah lewat, tanpa pernah diproses petugas.
        $r = $this->makeReservation(['reservation_date' => '2026-09-23', 'start_time' => '09:00', 'end_time' => '10:00']);

        $this->assertTrue($r->isExpired());
        $this->assertSame('Kedaluwarsa', $r->status_label);
        $this->assertSame('kedaluwarsa', $r->status_badge_class);
        $this->assertSame('pending', $r->status); // DB tidak berubah, murni label tampilan
    }

    public function test_pending_kedaluwarsa_tidak_lagi_mengunci_kuota_max_pending(): void
    {
        // 1 pending yang sudah kedaluwarsa (kemarin, tidak pernah diproses)...
        $this->makeReservation(['reservation_date' => '2026-09-23', 'start_time' => '09:00', 'end_time' => '10:00']);
        // ...+ 9 pending aktif (besok, belum lewat) = 10 baris pending di DB,
        // tapi cuma 9 yang masih "aktif" mengunci kuota.
        foreach (range(0, 8) as $i) {
            $this->makeReservation(['start_time' => sprintf('%02d:00', 10 + $i), 'end_time' => sprintf('%02d:30', 10 + $i)]);
        }

        $this->assertSame(10, Reservation::where('status', 'pending')->count());

        // Kuota masih longgar (9 aktif < 10) -> pengajuan baru harus tetap diterima.
        $this->book(['start_time' => '19:00', 'end_time' => '19:30'])->assertSessionHasNoErrors();
    }

    public function test_pending_yang_belum_kedaluwarsa_tetap_mengunci_kuota(): void
    {
        // Sanity check pembanding: 10 pending yang SEMUANYA masih aktif tetap memblokir,
        // supaya perubahan di atas tidak diam-diam melonggarkan aturan kuota yang lama.
        foreach (range(0, 9) as $i) {
            $this->makeReservation(['start_time' => sprintf('%02d:00', 7 + $i), 'end_time' => sprintf('%02d:30', 7 + $i)]);
        }

        $this->book(['start_time' => '18:00', 'end_time' => '19:00'])->assertSessionHasErrors('facility_id');
    }
}
