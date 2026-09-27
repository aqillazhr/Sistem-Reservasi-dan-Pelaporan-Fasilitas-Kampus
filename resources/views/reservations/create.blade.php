@extends('layouts.dashboard')

@section('title', 'Ajukan Reservasi')

@section('content')

    <div class="rsv">
        <h1>Ajukan reservasi</h1>
        <p class="sub">1. Pilih fasilitas · 2. Pilih tanggal dan slot · 3. Isi tujuan dan kirim.
            Reservasi bisa diajukan sampai {{ config('reservation.max_days_ahead') }} hari ke depan.</p>

        @if ($errors->has('facility_id') || session('error'))
            <div class="alert" style="background:#f3d1d1;color:#7a271a;padding:12px;border-radius:6px;margin-bottom:16px;">
                {{ $errors->first('facility_id') ?: session('error') }}
            </div>
        @endif

        {{-- 1. Cari / filter fasilitas --}}
        <div class="card-header">
            <h2>Cari fasilitas</h2>
            <form id="filterForm" onsubmit="event.preventDefault(); loadFacilities();">
                <div class="row">
                    <div><label for="q">Nama</label><input id="q" name="q" maxlength="100" placeholder="Cari nama fasilitas..."></div>
                    <div>
                        <label for="type_id">Tipe</label>
                        <select id="type_id" name="type_id">
                            <option value="">Semua tipe</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="location_id">Lokasi</label>
                        <select id="location_id" name="location_id">
                            <option value="">Semua lokasi</option>
                            @foreach ($locations->groupBy('scope_level') as $scope => $scopeLocations)
                                <optgroup label="{{ ucfirst($scope) }}">
                                    @foreach ($scopeLocations as $loc)
                                        @php
                                            $locLabel = collect([$loc->fakultas, $loc->gedung, $loc->ruangan])
                                                ->filter()->implode(' › ');
                                            $locLabel = $locLabel ?: 'Fasilitas Universitas';
                                        @endphp
                                        <option value="{{ $loc->id }}">{{ $locLabel }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div><label for="min_capacity">Kapasitas minimal</label><input id="min_capacity" name="min_capacity" type="number" min="1" placeholder="Contoh: 30"></div>
                    <div><button class="btn primary" type="submit">Cari</button></div>
                </div>
            </form>

            <div class="list" id="facilityList" style="margin-top:16px">
                {{-- Diisi via JS --}}
                <p class="muted">Memuat fasilitas...</p>
            </div>
            
            <div class="pager" id="facilityPager" style="display:none;">
                <button class="btn ghost" id="btnPrev">Sebelumnya</button>
                <button class="btn ghost" id="btnNext">Berikutnya</button>
            </div>
        </div>

        @include('reservations.partials.booking-modal')
    </div>

    <script>
    (function () {
        const fList = document.getElementById('facilityList');
        const fPager = document.getElementById('facilityPager');
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');

        // ------------------------------------------------------------
        // Cari / filter fasilitas — daftar kartunya dirender di server
        // (lihat reservations.partials.facility-list), JS cuma menyuntik
        // HTML jadi itu ke #facilityList. Klik "Pilih" ditangkap lewat
        // event delegation di #facilityList karena kartunya dibuat ulang
        // setiap fetch, bukan dibuat satu-satu lewat document.createElement.
        // ------------------------------------------------------------
        window.loadFacilities = function (page) {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form));
            [...params.keys()].forEach((k) => { if (!params.get(k)) params.delete(k); });
            params.set('page', page || 1);

            fetch('{{ route("pengguna.reservations.facilities-json") }}?' + params.toString())
                .then((r) => r.json())
                .then((res) => {
                    fList.innerHTML = res.html; // sudah di-escape Blade di server
                    fPager.style.display = 'flex';
                    btnPrev.disabled = !res.has_prev;
                    btnPrev.onclick = () => loadFacilities(res.prev_page);
                    btnNext.disabled = !res.has_next;
                    btnNext.onclick = () => loadFacilities(res.next_page);
                });
        };

        fList.addEventListener('click', function (ev) {
            const card = ev.target.closest('.facility-pick');
            if (! card) return;
            ev.preventDefault();
            // openReservationModal: fungsi global dari partial
            // reservations.partials.booking-modal (di-include di atas).
            // Tidak dikasih tanggal -> modal otomatis dibuka untuk hari ini.
            openReservationModal(card.dataset.id);
        });

        // Form auto-submit on change
        document.querySelectorAll('#filterForm input, #filterForm select').forEach((el) => {
            el.addEventListener('change', () => loadFacilities(1));
        });

        // initial load
        loadFacilities(1);
    })();
    </script>
@endsection
