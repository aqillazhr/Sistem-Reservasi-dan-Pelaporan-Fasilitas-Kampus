{{--
    Papan slot 30 menit, dirender dari $board (hasil ReservationService::slotBoard()).
    SEMUA keputusan (bebas/menunggu/terisi/lewat/kurang dari H-1, dan disabled
    atau tidak) sudah final dari server lewat kolom `state` — Blade di sini
    hanya menerjemahkannya jadi tombol, tidak menghitung ulang apa pun. Ini
    supaya tidak ada logika ganda di JS yang bisa berbeda hasil dari server
    (pernah kejadian: dropdown jam selesai sempat tidak ikut menyaring slot
    yang sudah tidak bisa dipilih, karena logikanya dobel-ditulis di JS).
--}}
@php $minAdvanceDays = (int) ceil(config('reservation.min_advance_hours') / 24); @endphp
@foreach ($board as $b)
    @php
        $stateText = match ($b['state']) {
            'pending' => 'menunggu',
            'approved' => 'terisi',
            'past' => 'lewat',
            'toosoon' => 'min. H-'.$minAdvanceDays,
            default => null,
        };
    @endphp
    <button type="button" class="slot {{ $b['state'] }}" data-start="{{ $b['start'] }}" data-end="{{ $b['end'] }}" @disabled($b['state'] !== 'free')>
        {{ str_replace(':', '.', $b['start']) }}–{{ str_replace(':', '.', $b['end']) }}
        @if ($stateText)<br><small>{{ $stateText }}</small>@endif
    </button>
@endforeach
