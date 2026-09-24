<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 40px;
        background: #faf7ff;
    }

    h1 {
        color: #54269a;
        font-size: 40px;
    }

    .facility-card {
        display: flex;
        align-items: center;
        gap: 20px;
        background: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .facility-image {
        width: 180px;
        height: 180px;
        flex-shrink: 0;
    }

    .facility-image img,
    .no-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #ddd;
        border-radius: 5px;
    }

    .facility-info {
        flex: 1;
    }

    .facility-info h2 {
        color: #54269a;
        margin-top: 0;
    }

    .facility-details div {
        display: grid;
        grid-template-columns: 130px 20px 1fr;
        margin: 5px 0;
    }

    .facility-details span::after {
        content: ":";
        margin-left: 10px;
    }

    .facility-arrow {
        font-size: 60px;
        color: #8f6bc1 !important;
        text-decoration: none !important;
        padding: 20px;
        line-height: 1;
    }
</style>
</head>

<body>

    <h1>Hasil Pencarian</h1>
    @if ($search || $capacity)
        <p>Hasil pencarian
            @if ($search) untuk: <strong>{{ $search }}</strong>@endif
            @if ($capacity) · kapasitas minimal <strong>{{ $capacity }}</strong> orang @endif
        </p>
    @endif
    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('facilities.index') }}">

        <input
            type="text"
            name="search"
            placeholder="Cari fasilitas..."
            value="{{ request('search') }}"
        >

        <input
            type="number"
            name="kapasitas"
            min="1"
            max="100000"
            placeholder="Kapasitas minimal"
            value="{{ $capacity }}"
            aria-label="Kapasitas minimal"
        >

        <button type="submit">Cari</button>

    </form>


    <hr>


    {{-- Hasil fasilitas --}}
    @forelse ($facilities as $facility)

        <div class="facility-card">

            {{-- Foto --}}
            <div class="facility-image">
                @if ($facility->photos->first())
                    <img
                        src="{{ asset('storage/' . $facility->photos->first()->file_path) }}"
                        alt="{{ $facility->name }}"
                    >
                @else
                    <div class="no-image"></div>
                @endif
            </div>

            {{-- Informasi --}}
            <div class="facility-info">

                <h2>{{ $facility->name }}</h2>

                <div class="facility-details">
                    <div>
                        <span>Tipe</span>
                        <strong>{{ $facility->type->name ?? '-' }}</strong>
                    </div>

                    <div>
                        <span>Kapasitas</span>
                        <strong>{{ $facility->capacity }} orang</strong>
                    </div>

                    <div>
                        <span>Status</span>
                        <strong>{{ ucfirst($facility->status) }}</strong>
                    </div>

                    <div>
                        <span>Ketersediaan</span>
                        <strong>Tersedia</strong>
                    </div>

                    <div>
                        <span>Lokasi</span>
                        <strong>{{ $facility->location->ruangan ?? '-' }}</strong>
                    </div>
                </div>

            </div>

            {{-- Panah / detail --}}
            <a href="{{ route('facilities.show', [
                'facility' => $facility,
                'from' => url()->full(),
            ]) }}"
            class="facility-arrow"
            >
                ›
            </a>

        </div>

    @empty

        @if ($search || $capacity)
            <p>Tidak ada fasilitas yang ditemukan.</p>
        @else
            <p>Ketik nama fasilitas atau isi kapasitas minimal, lalu klik Cari.</p>
        @endif

    @endforelse


    {{-- Pagination --}}
    @if ($facilities instanceof \Illuminate\Pagination\LengthAwarePaginator)
        {{ $facilities->links() }}
    @endif

</body>
</html>