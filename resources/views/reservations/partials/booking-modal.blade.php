{{--
    Modal "Ajukan Reservasi": pilih tanggal, papan slot, dan form pengajuan.
    Dipakai BERSAMA oleh:
      - reservations/create.blade.php (halaman cari fasilitas dulu)
      - facilities/show.blade.php (fasilitas sudah diketahui dari halaman itu)

    Cara pakai dari halaman manapun: cukup @include partial ini SEKALI, lalu
    panggil window.openReservationModal(facilityId) dari tombol "Ajukan
    Reservasi" di halaman itu — misalnya lewat atribut onclick, atau lewat
    event listener JS biasa. Parameter tanggal opsional; kalau tidak diisi,
    modal otomatis dibuka untuk hari ini.

    Partial ini sudah membawa reservations._styles sendiri (variabel warna
    & style .rsv yang dipakai badge/tombol/papan slot), supaya modal ini
    tetap tampil benar walau di-include di halaman yang bukan bagian dari
    modul reservasi.

    SEMUA logika (fasilitas aktif, slot bebas/menunggu/terisi/lewat/kurang
    dari H-1, transaction, cek bentrok) tetap satu-satunya sumber kebenaran
    di server — lihat ReservationController::slotBoardJson() & ::store(),
    ReservationService, dan reservations/partials/{facility-detail,board,
    time-options}.blade.php. Modal ini hanya menyuntikkan HTML hasil render
    itu ke DOM, tidak menghitung ulang apa pun sendiri.
--}}
@include('reservations._styles')

<style>
    /* override browser default dialog for full modal look */
    dialog#facilityModal {
        border: none;
        border-radius: 14px;
        padding: 0;
        width: min(1200px, 96vw);
        max-width: none;
        max-height: 92vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(38,15,69,.35);
        font-family: 'Sora', Helvetica, sans-serif;
    }
    dialog#facilityModal::backdrop {
        background: rgba(38,15,69,.55);
        backdrop-filter: blur(3px);
    }
    .modal-header {
        background: #260f45;
        color: #fff;
        padding: 20px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .modal-header h2 { margin: 0; font-size: 22px; color: #fff; }
    .modal-close {
        background: none;
        border: none;
        color: rgba(255,255,255,.7);
        font-size: 28px;
        cursor: pointer;
        line-height: 1;
        padding: 0 4px;
        transition: color .15s;
    }
    .modal-close:hover { color: #fff; }
    .modal-body { padding: 24px 28px; }

    .facility-detail-strip {
        display: flex;
        gap: 24px;
        background: var(--light);
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .facility-detail-strip .fds-item { flex: 1 1 140px; }
    .facility-detail-strip .fds-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #7a6497;
        margin-bottom: 3px;
    }
    .facility-detail-strip .fds-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--brand);
    }
</style>

<dialog id="facilityModal">
    <div class="modal-header">
        <h2 id="mName">Nama Fasilitas</h2>
        <button type="button" class="modal-close" id="btnCloseModal" title="Tutup">&times;</button>
    </div>

    <div class="modal-body rsv">
        <div class="facility-detail-strip" id="modalDetailStrip">
            <p class="muted">Memuat detail…</p>
        </div>

        {{-- Date Selector --}}
        @php
            $minDate = now()->startOfDay()->toDateString();
            $maxDate = now()->startOfDay()->addDays((int) config('reservation.max_days_ahead'))->toDateString();
        @endphp
        <div style="margin-bottom:16px">
            <label for="date">Tanggal</label>
            <div class="row" style="align-items:flex-end">
                <div>
                    <input id="date" type="date" value="" min="{{ $minDate }}" max="{{ $maxDate }}">
                </div>
            </div>
        </div>

        <div class="legend" style="margin-bottom:10px">
            <span><i style="background:var(--free)"></i>Tersedia</span>
            <span><i style="background:var(--wait)"></i>Menunggu konfirmasi</span>
            <span><i style="background:var(--busy)"></i>Terisi</span>
            <span><i style="background:var(--past)"></i>Sudah lewat / kurang dari H-{{ (int) ceil(config('reservation.min_advance_hours') / 24) }}</span>
        </div>
        <div class="board" id="board">
            <p class="muted">Memuat jadwal...</p>
        </div>
        <p class="muted" style="margin: 8px 0 20px">Klik slot awal, lalu slot akhir — atau pilih dari dropdown di bawah.</p>

        {{-- Booking Form --}}
        <form method="POST" action="{{ route('pengguna.reservations.store') }}" id="rsvForm" novalidate>
            @csrf
            <input type="hidden" name="facility_id" id="fFacilityId" value="">
            <input type="hidden" name="reservation_date" id="fDate" value="">

            <div class="row">
                <div>
                    <label for="start_time">Jam mulai</label>
                    <select id="start_time" name="start_time" required>
                        <option value="">Pilih</option>
                    </select>
                </div>
                <div>
                    <label for="end_time">Jam selesai</label>
                    <select id="end_time" name="end_time" required>
                        <option value="">Pilih</option>
                    </select>
                </div>
            </div>

            <label for="purpose">Tujuan penggunaan</label>
            <textarea id="purpose" name="purpose" rows="3" maxlength="1000" required></textarea>

            <div class="err" id="clientErr" role="alert" hidden></div>
            <p style="margin-top:16px">
                <button type="submit" class="btn primary" id="submitBtn">Ajukan reservasi</button>
            </p>
        </form>
    </div>
</dialog>

<dialog id="confirmDialog">
    <h2>Kirim pengajuan ini?</h2>
    <p id="confirmText" class="muted"></p>
    <div class="row">
        <button type="button" class="btn ghost" id="confirmNo">Periksa lagi</button>
        <button type="button" class="btn primary" id="confirmYes">Ya, ajukan</button>
    </div>
</dialog>

<script>
(function () {
    let currentFacilityId = null;
    let closeTimeStr = "20:00:00"; // default, di-overwrite saat fetch board

    const modal = document.getElementById('facilityModal');
    const detailStrip = document.getElementById('modalDetailStrip');
    const board = document.getElementById('board');
    const dateInput = document.getElementById('date');
    const fDateInput = document.getElementById('fDate');
    const s = document.getElementById('start_time');
    const e = document.getElementById('end_time');
    const p = document.getElementById('purpose');

    function todayStr() {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    // ------------------------------------------------------------
    // Satu-satunya pintu masuk publik partial ini. Dipanggil dari halaman
    // manapun yang sudah menyertakan partial ini, dengan facilityId yang
    // sudah diketahui halaman itu. dateVal opsional -> default hari ini.
    // ------------------------------------------------------------
    window.openReservationModal = function (facilityId, dateVal) {
        currentFacilityId = facilityId;
        dateVal = dateVal || todayStr();
        dateInput.value = dateVal;
        fDateInput.value = dateVal;
        document.getElementById('fFacilityId').value = facilityId;

        detailStrip.innerHTML = '<p class="muted">Memuat detail…</p>';
        board.innerHTML = '<p class="muted">Memuat jadwal...</p>';
        s.innerHTML = '<option value="">Pilih</option>';
        e.innerHTML = '<option value="">Pilih</option>';
        p.value = '';
        document.getElementById('clientErr').hidden = true;

        modal.showModal();
        loadBoard();
    };

    document.getElementById('btnCloseModal').addEventListener('click', () => modal.close());

    dateInput.addEventListener('change', function () {
        fDateInput.value = this.value;
        loadBoard();
    });

    // Detail fasilitas, papan slot, dan opsi jam mulai/selesai SEMUA
    // dirender di server (reservations.partials.facility-detail/board/
    // time-options) dan disuntikkan sebagai HTML jadi di sini — client tidak
    // menghitung ulang slot mana yang bebas/menunggu/terisi/lewat/kurang dari
    // H-1, itu keputusan server satu-satunya (lihat catatan di atas file).
    function loadBoard() {
        fetch(`{{ route('pengguna.reservations.slot-board-json') }}?facility=${currentFacilityId}&date=${dateInput.value}`)
            .then((r) => r.json())
            .then((res) => {
                document.getElementById('mName').textContent = res.facility_name;
                detailStrip.innerHTML = res.detail_html;
                board.innerHTML = res.board_html;
                s.innerHTML = res.start_options_html;
                e.innerHTML = res.end_options_html;
                closeTimeStr = res.closeTime;
                refreshFormDropdowns();
            });
    }

    // Batas jam selesai yang masih boleh dipilih untuk suatu jam mulai:
    // dibaca langsung dari tombol papan slot yang SUDAH dirender server
    // (data-start/data-end + kelas state-nya), bukan dari salinan data
    // terpisah yang bisa lupa disinkronkan dengan apa yang ditampilkan.
    function limitFor(start) {
        let lim = closeTimeStr;
        board.querySelectorAll('.slot').forEach((btn) => {
            const bStart = btn.dataset.start;
            if (bStart >= start && !btn.classList.contains('free') && bStart < lim) lim = bStart;
        });
        return lim;
    }

    function refreshFormDropdowns() {
        const lim = s.value ? limitFor(s.value) : closeTimeStr;
        Array.from(e.options).forEach((o) => {
            if (o.value) o.disabled = !s.value || o.value <= s.value || o.value > lim;
        });
        if (e.value && e.selectedOptions[0] && e.selectedOptions[0].disabled) e.value = '';

        board.querySelectorAll('.slot').forEach((c) => {
            c.classList.toggle('pick', !!(s.value && e.value && c.dataset.start >= s.value && c.dataset.end <= e.value));
        });
    }

    // Event delegation: tombol slot dibuat ulang server setiap fetch, jadi
    // listener dipasang sekali di kontainer #board yang persisten.
    board.addEventListener('click', function (ev) {
        const btn = ev.target.closest('.slot:not([disabled])');
        if (! btn) return;
        if (!s.value || e.value || btn.dataset.start < s.value) { s.value = btn.dataset.start; e.value = ''; }
        else { e.value = btn.dataset.end; }
        refreshFormDropdowns();
    });
    s.addEventListener('change', refreshFormDropdowns);
    e.addEventListener('change', refreshFormDropdowns);

    // ------------------------------------------------------------
    // Submit — validasi ringan di client untuk UX (server tetap penentu
    // akhir lewat StoreReservationRequest saat form ini benar-benar di-submit).
    // ------------------------------------------------------------
    const form = document.getElementById('rsvForm');
    const err = document.getElementById('clientErr');
    const dlg = document.getElementById('confirmDialog');
    let confirmed = false;

    function formatLabel(timeStr) {
        return timeStr.substring(0, 5).replace(':', '.');
    }

    function validate() {
        if (!s.value) return 'Pilih jam mulai.';
        if (!e.value) return 'Pilih jam selesai.';
        if (e.value <= s.value) return 'Jam selesai harus setelah jam mulai.';
        if (e.value > limitFor(s.value)) return 'Rentang waktu itu bentrok dengan slot yang sudah terisi.';
        if (p.value.trim().length < 5) return 'Tujuan penggunaan minimal 5 karakter.';
        return '';
    }

    form.addEventListener('submit', function (ev) {
        if (confirmed) return;
        ev.preventDefault();
        const msg = validate();
        err.hidden = !msg; err.textContent = msg;
        if (msg) return;

        document.getElementById('confirmText').textContent =
            document.getElementById('mName').textContent + ', ' + dateInput.value + ', ' + formatLabel(s.value) + '–' + formatLabel(e.value) + '.';
        dlg.showModal();
    });

    document.getElementById('confirmNo').addEventListener('click', () => dlg.close());
    document.getElementById('confirmYes').addEventListener('click', function () {
        confirmed = true; this.disabled = true; dlg.close();
        document.getElementById('submitBtn').disabled = true;
        form.requestSubmit();
    });
})();
</script>
