@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-4xl px-6 py-8">

    <h1 class="text-3xl font-bold">
        Pelaporan Kerusakan
    </h1>

    <p class="mt-2 text-gray-500">
        Laporkan kerusakan atau masalah pada fasilitas kampus.
    </p>

    @if ($errors->any())
        <div class="mt-6 rounded-lg bg-red-50 p-4 text-red-700">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('pengguna.reports.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="mt-8 space-y-6"
    >

        @csrf

        {{-- FASILITAS --}}

        <div class="rounded-xl bg-white p-6 shadow">

            <label class="block font-medium">
                Fasilitas
            </label>

            <select
                name="facility_id"
                required
                class="mt-2 w-full rounded-lg border-gray-300"
            >

                <option value="">
                    Pilih fasilitas
                </option>

                @foreach ($facilities as $facility)

                    <option
                        value="{{ $facility->id }}"
                    >
                        {{ $facility->name }}
                        -
                        {{ $facility->location->gedung ?? '-' }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- KATEGORI --}}

        <div class="rounded-xl bg-white p-6 shadow">

            <label class="block font-medium">
                Kategori Laporan
            </label>

            <select
                name="category"
                required
                class="mt-2 w-full rounded-lg border-gray-300"
            >

                <option value="">
                    Pilih kategori
                </option>

                <option value="Elektronik">
                    Elektronik
                </option>

                <option value="Furnitur">
                    Listrik
                </option>

                <option value="Bangunan">
                    AC
                </option>

                <option value="Fasilitas Ruangan">
                    Fasilitas Ruangan
                </option>

                <option value="Kebersihan">
                    Kebersihan
                </option>

                <option value="Jaringan">
                    Jaringan
                </option>

                <option value="Lainnya">
                    Lainnya
                </option>

            </select>

        </div>


        {{-- DESKRIPSI --}}

        <div class="rounded-xl bg-white p-6 shadow">

            <label class="block font-medium">
                Deskripsi Kerusakan
            </label>

            <textarea
                name="description"
                rows="6"
                class="mt-2 w-full rounded-lg border-gray-300"
                placeholder="Jelaskan kerusakan..."
            >{{ old('description') }}</textarea>

        </div>


        {{-- FOTO --}}

        <div class="rounded-xl bg-white p-6 shadow">

            <label class="block font-medium">
                Foto Bukti
            </label>

            <p class="mt-1 text-sm text-gray-500">
                Minimal 1 foto, maksimal 4 MB per foto.
            </p>

            <input
                type="file"
                name="photos[]"
                multiple
                required
                accept="image/*"
                class="mt-4 block w-full"
            >

        </div>


        <div class="flex justify-end">

            <button
                type="submit"
                class="rounded-lg bg-black px-6 py-3 font-semibold text-white"
            >
                Kirim Laporan
            </button>

        </div>

    </form>

</div>

@endsection