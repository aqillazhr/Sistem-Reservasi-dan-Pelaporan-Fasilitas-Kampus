@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-4xl px-6 py-8">

    <a
        href="{{ route('pengguna.reports.index') }}"
        class="text-sm text-gray-500"
    >
        ← Kembali
    </a>


    <div class="mt-6 flex items-center justify-between">

        <h1 class="text-3xl font-bold">
            Detail Laporan
        </h1>

        <span class="rounded-full bg-gray-100 px-4 py-2 font-semibold">
            {{ strtoupper($report->status) }}
        </span>

    </div>


    @if (session('status'))

        <div class="mt-6 rounded-lg bg-green-50 p-4 text-green-700">
            {{ session('status') }}
        </div>

    @endif


    <div class="mt-8 space-y-6">


        <div class="rounded-xl bg-white p-6 shadow">

            <h2 class="text-lg font-semibold">
                Informasi Laporan
            </h2>

            <div class="mt-5 space-y-3">

                <p>
                    <strong>Fasilitas:</strong>
                    {{ $report->facility->name }}
                </p>

                <p>
                    <strong>Kategori:</strong>
                    {{ $report->category }}
                </p>

                <p>
                    <strong>Deskripsi:</strong>
                    {{ $report->description }}
                </p>

            </div>

        </div>


        <div class="rounded-xl bg-white p-6 shadow">

            <h2 class="text-lg font-semibold">
                Foto Bukti
            </h2>

            <div class="mt-4 grid gap-4 md:grid-cols-2">

                @foreach ($report->photos as $photo)

                    <img
                        src="{{ asset(
                            'storage/' . $photo->file_path
                        ) }}"
                        class="h-64 w-full rounded-lg object-cover"
                    >

                @endforeach

            </div>

        </div>


        @if (
            $report->status === 'selesai'
            || $report->status === 'ditolak'
        )

            <div class="rounded-xl bg-white p-6 shadow">

                <h2 class="font-semibold">
                    Catatan Resolusi
                </h2>

                <p class="mt-3 whitespace-pre-line">
                    {{ $report->resolution_note ?? '-' }}
                </p>

                @if ($report->resolved_at)

                    <p class="mt-2 text-sm text-gray-500">
                        Ditutup:
                        {{ $report->resolved_at->format('d M Y H:i') }}
                    </p>

                @endif

            </div>

        @endif

    </div>

</div>

@endsection