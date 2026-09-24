@extends('layouts.dashboard')

@section('title', 'Ajukan Reservasi')

@section('content')

    @include('reservations._styles')
    @php
        $keep = \Illuminate\Support\Arr::only($filters, ['q', 'type_id', 'location_id', 'min_capacity']);
        $label = fn ($t) => str_replace(':', '.', $t);
    @endphp
    <div class="rsv">
        <h1>Ajukan reservasi</h1>
        <p class="sub">1. Pilih fasilitas · 2. Pilih tanggal dan slot · 3. Isi tujuan dan kirim.
            Reservasi bisa diajukan sampai {{ config('reservation.max_days_ahead') }} hari ke depan.</p>

        @if ($errors->has('facility_id'))
            <div class="alert" role="alert">{{ $errors->first('facility_id') }}</div>
        @endif

        {{-- 1. Cari / filter fasilitas --}}
        <div class="card-header">
            <h2>Cari fasilitas</h2>
            <form method="GET" action="{{ route('pengguna.reservations.create') }}">
                <div class="row">
                    <div><label for="q">Nama</label><input id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Cari nama fasilitas..."></div>
                    <div>
                        <label for="type_id">Tipe</label>
                        <select id="type_id" name="type_id">
                            <option value="">Semua tipe</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}" @selected(($filters['type_id'] ?? null) == $t->id)>{{ $t->name }}</option>
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
                                        <option value="{{ $loc->id }}" @selected(($filters['location_id'] ?? null) == $loc->id)>
                                            {{ $locLabel }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div><label for="min_capacity">Kapasitas minimal</label><input id="min_capacity" name="min_capacity" type="number" min="1" value="{{ $filters['min_capacity'] ?? '' }}" placeholder="Contoh: 30"></div>
                    <div><button class="btn primary" type="submit">Cari</button></div>
                </div>
            </form>

            <div class="list" style="margin-top:16px">
                @forelse ($facilities as $f)
                    <a class="card item" href="{{ route('pengguna.reservations.create', $keep + ['facility' => $f->id, 'date' => $date]) }}"
                       @if ($selected && $selected->id === $f->id) aria-current="true" style="border-color:var(--brand)" @endif>
                        <div>
                            <strong>{{ $f->name }}</strong>
                            <div class="muted">{{ $f->type->name ?? '' }} · kapasitas {{ $f->capacity }} ·
                                {{ implode(', ', array_filter([$f->location->gedung ?? null, $f->location->ruangan ?? null, $f->location->fakultas ?? null])) ?: 'Fasilitas universitas' }}</div>
                        </div>
                        <span class="btn">Pilih</span>
                    </a>
                @empty
                    <p class="muted">Tidak ada fasilitas yang cocok. Coba ubah filter.</p>
                @endforelse
            </div>
            <div class="pager">
                @if ($facilities->previousPageUrl())<a class="btn ghost" href="{{ $facilities->previousPageUrl() }}">Sebelumnya</a>@else<span></span>@endif
                @if ($facilities->nextPageUrl())<a class="btn ghost" href="{{ $facilities->nextPageUrl() }}">Berikutnya</a>@endif
            </div>
        </div>

        @if ($selected)
        {{-- ── Modal: Detail + Jadwal + Booking ── --}}
        <style>
            /* override browser default dialog for full modal look */
            dialog#facilityModal {
                border: none;
                border-radius: 14px;
                padding: 0;
                width: min(860px, 95vw);
                max-height: 90vh;
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

            /* Facility detail strip */
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
            {{-- Header --}}
            <div class="modal-header">
                <h2>{{ $selected->name }}</h2>
                <a href="{{ route('pengguna.reservations.create', array_filter(array_merge($keep, ['date' => $date]))) }}"
                   class="modal-close" title="Tutup">&times;</a>
            </div>

            <div class="modal-body rsv">
                {{-- Detail strip --}}
                <div class="facility-detail-strip">
                    <div class="fds-item">
                        <div class="fds-label">Tipe</div>
                        <div class="fds-value">{{ $selected->type->name ?? '—' }}</div>
                    </div>
                    <div class="fds-item">
                        <div class="fds-label">Kapasitas</div>
                        <div class="fds-value">{{ $selected->capacity }} orang</div>
                    </div>
                    <div class="fds-item">
                        <div class="fds-label">Lokasi</div>
                        <div class="fds-value">
                            @php
                                $loc = $selected->location;
                                $locParts = array_filter([
                                    $loc->ruangan ?? null,
                                    $loc->gedung   ?? null,
                                    $loc->fakultas ?? null,
                                ]);
                                echo implode(', ', $locParts) ?: 'Universitas';
                            @endphp
                        </div>
                    </div>
                    @if ($selected->description)
                    <div class="fds-item" style="flex: 2 1 240px;">
                        <div class="fds-label">Deskripsi</div>
                        <div class="fds-value" style="font-weight:400; font-size:14px;">{{ $selected->description }}</div>
                    </div>
                    @endif
                </div>

                {{-- Pilih tanggal --}}
                <form method="GET" action="{{ route('pengguna.reservations.create') }}" style="margin-bottom:16px">
                    @foreach ($keep as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                    <input type="hidden" name="facility" value="{{ $selected->id }}">
                    <label for="date">Tanggal</label>
                    <div class="row" style="align-items:flex-end">
                        <div>
                            <input id="date" name="date" type="date"
                                   value="{{ $date }}" min="{{ $minDate }}" max="{{ $maxDate }}"
                                   onchange="this.form.submit()">
                        </div>
                        <noscript><button class="btn ghost" type="submit">Lihat jadwal</button></noscript>
                    </div>
                </form>

                {{-- Legenda + slot board --}}
                <div class="legend" style="margin-bottom:10px">
                    <span><i style="background:var(--free)"></i>Tersedia</span>
                    <span><i style="background:var(--wait)"></i>Menunggu konfirmasi</span>
                    <span><i style="background:var(--busy)"></i>Terisi</span>
                    <span><i style="background:var(--past)"></i>Sudah lewat</span>
                </div>
                <div class="board" id="board">
                    @foreach ($board as $b)
                        <button type="button" class="slot {{ $b['state'] }}"
                                data-start="{{ $b['start'] }}" data-end="{{ $b['end'] }}"
                                @disabled($b['state'] !== 'free')>
                            {{ $label($b['start']) }}–{{ $label($b['end']) }}
                            @if ($b['state'] !== 'free')<br><small>{{ ['pending'=>'menunggu','approved'=>'terisi','past'=>'lewat'][$b['state']] }}</small>@endif
                        </button>
                    @endforeach
                </div>
                <p class="muted" style="margin: 8px 0 20px">Klik slot awal, lalu slot akhir — atau pilih dari dropdown di bawah.</p>

                {{-- Form booking --}}
                <form method="POST" action="{{ route('pengguna.reservations.store') }}" id="rsvForm" novalidate>
                    @csrf
                    <input type="hidden" name="facility_id" value="{{ $selected->id }}">
                    <input type="hidden" name="reservation_date" value="{{ $date }}">

                    <div class="row">
                        <div>
                            <label for="start_time">Jam mulai</label>
                            <select id="start_time" name="start_time" required>
                                <option value="">Pilih</option>
                                @foreach ($board as $b)
                                    @if ($b['state'] === 'free')
                                        <option value="{{ $b['start'] }}" @selected(old('start_time') === $b['start'])>{{ $label($b['start']) }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('start_time')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label for="end_time">Jam selesai</label>
                            <select id="end_time" name="end_time" required>
                                <option value="">Pilih</option>
                                @foreach ($board as $b)
                                    <option value="{{ $b['end'] }}" @selected(old('end_time') === $b['end'])>{{ $label($b['end']) }}</option>
                                @endforeach
                            </select>
                            @error('end_time')<div class="err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    @error('reservation_date')<div class="err">{{ $message }}</div>@enderror

                    <label for="purpose">Tujuan penggunaan</label>
                    <textarea id="purpose" name="purpose" rows="3" maxlength="1000" required>{{ old('purpose') }}</textarea>
                    @error('purpose')<div class="err">{{ $message }}</div>@enderror

                    <div class="err" id="clientErr" role="alert" hidden></div>
                    <p style="margin-top:16px">
                        <button type="submit" class="btn primary" id="submitBtn">Ajukan reservasi</button>
                    </p>
                </form>
            </div>
        </dialog>

        {{-- Konfirmasi submit --}}
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
                // Auto-buka modal karena fasilitas sudah dipilih
                document.getElementById('facilityModal').showModal();

                var board = @json($board);
                var form  = document.getElementById('rsvForm');
                var s = form.elements.start_time, e = form.elements.end_time, p = form.elements.purpose;
                var err   = document.getElementById('clientErr');
                var dlg   = document.getElementById('confirmDialog');
                var chips = document.querySelectorAll('#board .slot'), confirmed = false;
                var close = @json(config('reservation.close_time'));
                var dot   = function (t) { return t.replace(':', '.'); };

                function limitFor(start) {
                    var lim = close;
                    board.forEach(function (b) {
                        if (b.start >= start && b.state !== 'free' && b.start < lim) lim = b.start;
                    });
                    return lim;
                }
                function refresh() {
                    var lim = s.value ? limitFor(s.value) : close;
                    Array.prototype.forEach.call(e.options, function (o) {
                        if (o.value) o.disabled = !s.value || o.value <= s.value || o.value > lim;
                    });
                    if (e.value && e.selectedOptions[0].disabled) e.value = '';
                    chips.forEach(function (c) {
                        c.classList.toggle('pick', !!(s.value && e.value && c.dataset.start >= s.value && c.dataset.end <= e.value));
                    });
                }
                chips.forEach(function (c) {
                    c.addEventListener('click', function () {
                        if (!s.value || e.value || c.dataset.start < s.value) { s.value = c.dataset.start; e.value = ''; }
                        else { e.value = c.dataset.end; }
                        refresh();
                    });
                });
                s.addEventListener('change', refresh);
                e.addEventListener('change', refresh);
                refresh();

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
                    var msg = validate();
                    err.hidden = !msg; err.textContent = msg;
                    if (msg) return;
                    document.getElementById('confirmText').textContent =
                        @json($selected->name) + ', {{ $date }}, ' + dot(s.value) + '–' + dot(e.value) + '.';
                    dlg.showModal();
                });
                document.getElementById('confirmNo').addEventListener('click', function () { dlg.close(); });
                document.getElementById('confirmYes').addEventListener('click', function () {
                    confirmed = true; this.disabled = true; dlg.close();
                    document.getElementById('submitBtn').disabled = true;
                    form.requestSubmit();
                });
            })();
        </script>
        @endif
    </div>
@endsection
