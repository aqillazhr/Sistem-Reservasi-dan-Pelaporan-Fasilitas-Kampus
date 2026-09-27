{{-- Strip detail fasilitas di dalam modal — dirender server, client tinggal suntik. --}}
@php
    $locParts = array_filter([
        $facility->location->ruangan ?? null,
        $facility->location->gedung ?? null,
        $facility->location->fakultas ?? null,
    ]);
    $locLabel = $locParts ? implode(', ', $locParts) : 'Universitas';
@endphp
<div class="fds-item">
    <div class="fds-label">Tipe</div>
    <div class="fds-value">{{ $facility->type->name ?? '—' }}</div>
</div>
<div class="fds-item">
    <div class="fds-label">Kapasitas</div>
    <div class="fds-value">{{ $facility->capacity }} orang</div>
</div>
<div class="fds-item">
    <div class="fds-label">Lokasi</div>
    <div class="fds-value">{{ $locLabel }}</div>
</div>
@if ($facility->description)
    <div class="fds-item" style="flex: 2 1 240px;">
        <div class="fds-label">Deskripsi</div>
        <div class="fds-value" style="font-weight:400; font-size:14px;">{{ $facility->description }}</div>
    </div>
@endif
