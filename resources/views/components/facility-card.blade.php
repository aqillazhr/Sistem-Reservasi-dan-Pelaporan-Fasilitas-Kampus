{{-- resources/views/components/facility-card.blade.php --}}
@props(['facility'])

<div class="facility-card">
    @if ($facility->photos->first())
        <img src="{{ asset('storage/' . $facility->photos->first()->file_path) }}"
             alt="{{ $facility->name }}" class="facility-card__photo">
    @else
        <div class="facility-card__photo"></div>
    @endif

    <div class="facility-card__info">
        <p class="facility-card__name">{{ $facility->name }}</p>
        <div class="facility-card__details">
            <span>Tipe</span><span>:</span><span>{{ $facility->type->name ?? '-' }}</span>
            <span>Kapasitas</span><span>:</span><span>{{ $facility->capacity }} orang</span>
            <span>Status</span><span>:</span><span>{{ ucfirst($facility->status) }}</span>
            <span>Ketersediaan</span><span>:</span>
            <span>{{ $facility->status === 'aktif' ? 'Tersedia' : 'Tidak tersedia' }}</span>
            <span>Lokasi</span><span>:</span>
            <span>{{ collect([$facility->location->ruangan, $facility->location->gedung, $facility->location->fakultas])->filter()->implode(', ') }}</span>
        </div>
    </div>

    <a href="{{ route('facilities.show', $facility) }}" class="facility-card__arrow" aria-label="Lihat detail">›</a>
</div>