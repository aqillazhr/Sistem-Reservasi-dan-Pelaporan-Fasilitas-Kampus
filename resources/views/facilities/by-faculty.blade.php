<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $faculty }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f3ff;
            margin: 0;
            padding: 40px;
            color: #54269a;
        }

        h1 {
            margin-bottom: 30px;
        }

        .facility-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .facility-card h2 {
            margin-top: 0;
        }
    </style>
</head>

<body>

    <h1>{{ $faculty }}</h1>

    @forelse ($facilities as $facility)

        <div class="facility-card">

            <h2>{{ $facility->name }}</h2>

            <p>
                <strong>Tipe:</strong>
                {{ $facility->type->name ?? '-' }}
            </p>

            <p>
                <strong>Kapasitas:</strong>
                {{ $facility->capacity }} orang
            </p>

            <p>
                <strong>Status:</strong>
                {{ $facility->status }}
            </p>

            <p>
                <strong>Lokasi:</strong>
                {{ $facility->location->fakultas ?? '-' }}
            </p>

            <a href="{{ route('facilities.show', $facility) }}">
                Lihat Detail
            </a>

        </div>

    @empty

        <p>
            Belum ada fasilitas di fakultas ini.
        </p>

    @endforelse

</body>
</html>