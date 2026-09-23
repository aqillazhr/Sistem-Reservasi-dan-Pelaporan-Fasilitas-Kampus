<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facility->name }}</title>
</head>

<body>

    <a href="{{ request('from', route('facilities.index')) }}"
    style="font-size: 32px; text-decoration: none;"
    >
        ←
    </a>

    <h1>{{ $facility->name }}</h1>

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
        Deskripsi:
        {{ $facility->description ?? '-' }}
    </p>

    <p>
        Status:
        {{ $facility->status }}
    </p>

    <h2>Foto</h2>

    @forelse ($facility->photos as $photo)
        <img
            src="{{ asset('storage/' . $photo->file_path) }}"
            alt="{{ $facility->name }}"
            width="200"
        >
    @empty
        <p>Belum ada foto fasilitas.</p>
    @endforelse

</body>
</html>