{{--
    Daftar kartu fasilitas hasil pencarian, dirender di server (Blade
    auto-escape) lalu disuntikkan sebagai HTML jadi oleh JS. Sebelumnya field
    seperti nama/lokasi fasilitas dikirim sebagai JSON lalu digabung ke
    innerHTML lewat template literal di JS TANPA di-escape — kalau admin
    pernah mengisi nama fasilitas dengan tag HTML/script, itu akan ikut
    dieksekusi di browser semua pengguna yang membuka pencarian ini. Dengan
    render di sini, {{ }} Blade otomatis meng-escape nilainya.
--}}
@forelse ($facilities as $f)
    @php
        $locParts = array_filter([
            $f->location->ruangan ?? null,
            $f->location->gedung ?? null,
            $f->location->fakultas ?? null,
        ]);
        $locLabel = $locParts ? implode(', ', $locParts) : 'Universitas';
    @endphp
    <a href="#" class="card item facility-pick" data-id="{{ $f->id }}">
        <div>
            <strong>{{ $f->name }}</strong>
            <div class="muted">{{ $f->type->name ?? '' }} · kapasitas {{ $f->capacity }} · {{ $locLabel }}</div>
        </div>
        <span class="btn">Pilih</span>
    </a>
@empty
    <p class="muted">Tidak ada fasilitas yang cocok. Coba ubah filter.</p>
@endforelse
