<div style="
    flex: 1; min-width: 465px; background: #fff; border: 1px solid #9747FF; border-radius: 10px;
    padding: 18px 20px; font-family: 'Sora', Helvetica, sans-serif;">
    <h2 style="margin: 0 0 14px; font-size: 25px; font-weight: 700; color: #000;">Antrian Reservasi</h2>

    @forelse ($queue as $r)
        <div style="padding: 14px 0; border-top: 1px solid #BD93F8; @if ($loop->first) border-top: none; @endif position: relative;">
            <div style="font-size: 16px; font-weight: 700; color: #000; margin-bottom: 4px;">{{ $r->purpose }}</div>
            <div style="font-size: 13px; color: #525151; margin-bottom: 2px;">Pengaju: {{ $r->user->name }}</div>
            @php
                $locs = array_filter([$r->facility->location->fakultas, $r->facility->location->ruangan]);
            @endphp
            <div style="font-size: 13px; color: #525151; margin-bottom: 2px;">{{ implode(', ', $locs) }}</div>
            <div style="font-size: 13px; color: #525151; margin-bottom: 2px;">{{ $r->reservation_date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
            <div style="font-size: 13px; color: #525151;">{{ str_replace(':', '.', $r->start_short) }} - {{ str_replace(':', '.', $r->end_short) }} WIB</div>

            <div style="position: absolute; top: 14px; right: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                <span style="font-size: 10px; font-weight: 600; color: #000;">{{ $r->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                <div style="display: flex; gap: 8px;">
                    <form method="POST" action="{{ route('petugas.reservations.approve', $r) }}"
                          class="form-confirm" data-confirm-title="Setujui Reservasi" data-confirm-msg="Apakah Anda yakin ingin menyetujui reservasi {{ $r->facility->name }} ini?" style="margin: 0;">
                        @csrf
                        <button type="submit" title="Setuju" style="border:0; cursor:pointer; width: 36px; height: 36px; border-radius: 8px; background: #C3FFC3; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1E8E3E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </button>
                    </form>
                    <button type="button" title="Tolak"
                            data-reason-url="{{ route('petugas.reservations.reject', $r) }}"
                            data-reason-field="note"
                            data-reason-title="Tolak reservasi {{ $r->facility->name }}?"
                            style="border:0; cursor:pointer; width: 36px; height: 36px; border-radius: 8px; background: #FF8C8C; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7A271A" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <p style="color: rgba(0,0,0,.5); margin: 0;">Tidak ada reservasi yang menunggu.</p>
    @endforelse

    @if ($queue->isNotEmpty())
        <a href="{{ route('petugas.reservations.index') }}" style="display:block;margin-top:10px;color:#9747FF;font-weight:600;text-decoration:none;">
            Lihat semua &rarr;
        </a>
    @endif
</div>

@include('reservations.partials.reason-modal')
