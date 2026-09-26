@extends('layouts.dashboard')

@section('title', 'Ajukan Reservasi')

@section('content')

    @include('reservations._styles')
    
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

        {{-- ── Modal: Detail + Jadwal + Booking ── --}}
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
                <div class="facility-detail-strip">
                    <div class="fds-item">
                        <div class="fds-label">Tipe</div>
                        <div class="fds-value" id="mType">—</div>
                    </div>
                    <div class="fds-item">
                        <div class="fds-label">Kapasitas</div>
                        <div class="fds-value" id="mCapacity">—</div>
                    </div>
                    <div class="fds-item">
                        <div class="fds-label">Lokasi</div>
                        <div class="fds-value" id="mLocation">—</div>
                    </div>
                    <div class="fds-item" style="flex: 2 1 240px;" id="mDescWrap">
                        <div class="fds-label">Deskripsi</div>
                        <div class="fds-value" id="mDesc" style="font-weight:400; font-size:14px;">—</div>
                    </div>
                </div>

                {{-- Date Selector --}}
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
                    <span><i style="background:var(--past)"></i>Sudah lewat</span>
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
    </div>

    <script>
    (function() {
        let currentUrl = '{{ route("pengguna.reservations.facilities-json") }}';
        let currentFacilityId = null;
        let currentBoardData = [];
        let closeTimeStr = "20:00:00"; // default, di-overwrite saat fetch board
        
        const fList = document.getElementById('facilityList');
        const fPager = document.getElementById('facilityPager');
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        
        // Modal elements
        const modal = document.getElementById('facilityModal');
        const board = document.getElementById('board');
        const dateInput = document.getElementById('date');
        const fDateInput = document.getElementById('fDate');
        const s = document.getElementById('start_time');
        const e = document.getElementById('end_time');
        const p = document.getElementById('purpose');
        
        function formatLabel(timeStr) {
            return timeStr.substring(0, 5).replace(':', '.');
        }

        window.loadFacilities = function(url) {
            url = url || currentUrl;
            let form = document.getElementById('filterForm');
            let formData = new FormData(form);
            let params = new URLSearchParams();
            for(let [k,v] of formData.entries()) {
                if(v) params.append(k,v);
            }
            
            let fetchUrl = url;
            if(!url.includes('?')) {
                fetchUrl += '?' + params.toString();
            } else if (url === currentUrl) {
                fetchUrl = url.split('?')[0] + '?' + params.toString();
            }

            fetch(fetchUrl)
                .then(r => r.json())
                .then(res => {
                    currentUrl = fetchUrl; // save current search URL for pagination base if needed
                    fList.innerHTML = '';
                    if(res.data.length === 0) {
                        fList.innerHTML = '<p class="muted">Tidak ada fasilitas yang cocok. Coba ubah filter.</p>';
                        fPager.style.display = 'none';
                        return;
                    }
                    
                    res.data.forEach(f => {
                        let a = document.createElement('a');
                        a.className = 'card item';
                        a.href = '#';
                        a.onclick = function(ev) {
                            ev.preventDefault();
                            openModal(f.id, dateInput.value || '{{ $minDate }}');
                        };
                        a.innerHTML = `
                            <div>
                                <strong>${f.name}</strong>
                                <div class="muted">${f.type} · kapasitas ${f.capacity} · ${f.location}</div>
                            </div>
                            <span class="btn">Pilih</span>
                        `;
                        fList.appendChild(a);
                    });
                    
                    fPager.style.display = 'flex';
                    btnPrev.disabled = !res.prev_page_url;
                    btnPrev.onclick = () => loadFacilities(res.prev_page_url);
                    btnNext.disabled = !res.next_page_url;
                    btnNext.onclick = () => loadFacilities(res.next_page_url);
                });
        };

        // Form auto-submit on change
        document.querySelectorAll('#filterForm input, #filterForm select').forEach(el => {
            el.addEventListener('change', () => loadFacilities('{{ route("pengguna.reservations.facilities-json") }}'));
        });

        function openModal(id, dateVal) {
            currentFacilityId = id;
            dateInput.value = dateVal;
            fDateInput.value = dateVal;
            document.getElementById('fFacilityId').value = id;
            
            // clear board & form
            board.innerHTML = '<p class="muted">Memuat jadwal...</p>';
            s.innerHTML = '<option value="">Pilih</option>';
            e.innerHTML = '<option value="">Pilih</option>';
            p.value = '';
            
            modal.showModal();
            loadBoard();
        }
        
        document.getElementById('btnCloseModal').addEventListener('click', () => modal.close());
        
        dateInput.addEventListener('change', function() {
            fDateInput.value = this.value;
            loadBoard();
        });

        function loadBoard() {
            fetch(`{{ route('pengguna.reservations.slot-board-json') }}?facility=${currentFacilityId}&date=${dateInput.value}`)
                .then(r => r.json())
                .then(res => {
                    // Update header
                    document.getElementById('mName').textContent = res.facility.name;
                    document.getElementById('mType').textContent = res.facility.type || '—';
                    document.getElementById('mCapacity').textContent = res.facility.capacity + ' orang';
                    document.getElementById('mLocation').textContent = res.facility.location || '—';
                    
                    if(res.facility.description) {
                        document.getElementById('mDescWrap').style.display = 'block';
                        document.getElementById('mDesc').textContent = res.facility.description;
                    } else {
                        document.getElementById('mDescWrap').style.display = 'none';
                    }
                    
                    closeTimeStr = res.closeTime;
                    currentBoardData = res.board;
                    
                    renderBoard();
                });
        }
        
        function limitFor(start) {
            let lim = closeTimeStr;
            currentBoardData.forEach(b => {
                if (b.start >= start && b.state !== 'free' && b.start < lim) lim = b.start;
            });
            return lim;
        }

        function refreshFormDropdowns() {
            let lim = s.value ? limitFor(s.value) : closeTimeStr;
            Array.from(e.options).forEach(o => {
                if (o.value) o.disabled = !s.value || o.value <= s.value || o.value > lim;
            });
            if (e.value && e.selectedOptions[0].disabled) e.value = '';
            
            document.querySelectorAll('#board .slot').forEach(c => {
                c.classList.toggle('pick', !!(s.value && e.value && c.dataset.start >= s.value && c.dataset.end <= e.value));
            });
        }

        function renderBoard() {
            board.innerHTML = '';
            s.innerHTML = '<option value="">Pilih</option>';
            e.innerHTML = '<option value="">Pilih</option>';
            
            currentBoardData.forEach(b => {
                let btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `slot ${b.state}`;
                btn.dataset.start = b.start;
                btn.dataset.end = b.end;
                btn.disabled = b.state !== 'free';
                
                let stateLabel = '';
                if(b.state === 'pending') stateLabel = '<br><small>menunggu</small>';
                if(b.state === 'approved') stateLabel = '<br><small>terisi</small>';
                if(b.state === 'past') stateLabel = '<br><small>lewat</small>';
                
                btn.innerHTML = `${formatLabel(b.start)}–${formatLabel(b.end)}${stateLabel}`;
                
                btn.addEventListener('click', () => {
                    if (!s.value || e.value || btn.dataset.start < s.value) { s.value = btn.dataset.start; e.value = ''; }
                    else { e.value = btn.dataset.end; }
                    refreshFormDropdowns();
                });
                
                board.appendChild(btn);
                
                if(b.state === 'free') {
                    let optS = document.createElement('option');
                    optS.value = b.start; optS.textContent = formatLabel(b.start);
                    s.appendChild(optS);

                    // Jam selesai E hanya valid kalau slot TEPAT SEBELUM E ini ('free')
                    // itu sendiri free — kalau slotnya sudah lewat/terisi/menunggu,
                    // reservasi tidak mungkin berakhir tepat di situ. Makanya opsi ini
                    // ikut dibuat di dalam blok `free` yang sama seperti jam mulai,
                    // bukan untuk setiap slot seperti sebelumnya.
                    let optE = document.createElement('option');
                    optE.value = b.end; optE.textContent = formatLabel(b.end);
                    e.appendChild(optE);
                }
            });
            
            s.removeEventListener('change', refreshFormDropdowns);
            e.removeEventListener('change', refreshFormDropdowns);
            s.addEventListener('change', refreshFormDropdowns);
            e.addEventListener('change', refreshFormDropdowns);
        }

        // Form Submission
        let form = document.getElementById('rsvForm');
        let err = document.getElementById('clientErr');
        let dlg = document.getElementById('confirmDialog');
        let confirmed = false;
        
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
            let msg = validate();
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

        // initial load
        loadFacilities();
    })();
    </script>
@endsection
