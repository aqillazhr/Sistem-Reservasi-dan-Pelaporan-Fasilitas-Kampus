<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Fasilitas</title>
</head>

<body>

    <h1>Daftar Fasilitas</h1>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('facilities.index') }}">

        {{-- Search nama fasilitas --}}
        <input
            type="text"
            name="search"
            placeholder="Cari fasilitas..."
            value="{{ request('search') }}"
        >

        {{-- Filter tipe --}}
        <select name="type_id">
            <option value="">Semua Tipe</option>

            @foreach (\App\Models\FacilityType::all() as $type)
                <option
                    value="{{ $type->id }}"
                    {{ request('type_id') == $type->id ? 'selected' : '' }}
                >
                    {{ $type->name }}
                </option>
            @endforeach
        </select>

        {{-- Filter kapasitas --}}
        <input
            type="number"
            name="min_capacity"
            placeholder="Kapasitas minimum"
            min="1"
            value="{{ request('min_capacity') }}"
        >

        <button type="submit">Cari</button>

        <a href="{{ route('facilities.index') }}">Reset</a>

    </form>


    <hr>


    {{-- Daftar fasilitas --}}
    @forelse ($facilities as $facility)

        <div>
            <h2>{{ $facility->name }}</h2>

            <p>
                Tipe:
                {{ $facility->type->name ?? '-' }}
            </p>

            <p>
                Lokasi:
                {{ $facility->location->name ?? '-' }}
            </p>

            <p>
                Kapasitas:
                {{ $facility->capacity }} orang
            </p>

            <p>
                Status:
                {{ $facility->status }}
            </p>

            <a href="{{ route('facilities.show', $facility) }}">
                Lihat Detail
            </a>
        </div>

        <hr>

    @empty

        <p>Tidak ada fasilitas yang ditemukan.</p>

    @endforelse


    {{-- Pagination --}}
    {{ $facilities->links() }}

</body>
</html>