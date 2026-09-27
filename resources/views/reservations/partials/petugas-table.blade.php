@php
    $label = fn ($t) => str_replace(':', '.', $t);
@endphp
<div class="table-wrap">
<table>
    <thead>
        <tr>
            <th>Pemohon</th>
            <th>Fasilitas</th>
            <th>Tipe</th>
            <th>Jadwal</th>
            <th>Tujuan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($reservations as $r)
            <tr class="row-click" data-href="{{ route('petugas.reservations.show', $r) }}">
                <td>
                    <div class="name">{{ $r->user->name }}</div>
                    <div class="muted">{{ ucfirst($r->user->user_type ?? '') }}</div>
                </td>
                <td>{{ $r->facility->name }}</td>
                <td>{{ $r->facility->type->name ?? '-' }}</td>
                <td>
                    <div>{{ $r->reservation_date->locale('id')->isoFormat('D MMM YYYY') }}</div>
                    <div class="muted">{{ $label($r->start_short) }} - {{ $label($r->end_short) }} WIB</div>
                </td>
                <td>{{ \Illuminate\Support\Str::limit($r->purpose, 40) }}</td>
                <td><span class="badge {{ $r->status_badge_class }}">{{ $r->status_label }}</span></td>
                <td>
                    <div class="actions">
                        @if ($r->status === 'pending')
                            <form method="POST" action="{{ route('petugas.reservations.approve', $r) }}"
                                  class="form-confirm" data-confirm-title="Setujui Reservasi" data-confirm-msg="Apakah Anda yakin ingin menyetujui reservasi {{ $r->facility->name }} ini?">
                                @csrf
                                <button type="submit" class="btn-approve">Setuju</button>
                            </form>
                            <button type="button" class="btn-reject"
                                    data-reason-url="{{ route('petugas.reservations.reject', $r) }}"
                                    data-reason-field="note"
                                    data-reason-title="Tolak reservasi {{ $r->facility->name }}?">Tolak</button>
                        @elseif ($r->status === 'approved' && ! $r->isFinished())
                            <button type="button" class="btn-cancel"
                                    data-reason-url="{{ route('petugas.reservations.petugas-cancel', $r) }}"
                                    data-reason-field="cancellation_reason"
                                    data-reason-title="Batalkan reservasi {{ $r->facility->name }}?">Batalkan</button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Tidak ada reservasi di kategori ini.</td></tr>
        @endforelse
    </tbody>
</table>
</div>

<div class="pager">
    @if ($reservations->previousPageUrl())
        <a href="{{ $reservations->previousPageUrl() }}">&larr; Sebelumnya</a>
    @else <span></span> @endif
    @if ($reservations->nextPageUrl())
        <a href="{{ $reservations->nextPageUrl() }}">Berikutnya &rarr;</a>
    @endif
</div>
