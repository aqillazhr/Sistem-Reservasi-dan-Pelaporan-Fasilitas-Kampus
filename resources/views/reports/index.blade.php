@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-6xl px-6 py-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold">
                Laporan Saya
            </h1>

            <p class="mt-2 text-gray-500">
                Pantau status laporan kerusakan fasilitas.
            </p>

        </div>

        <a
            href="{{ route('pengguna.reports.create') }}"
            class="rounded-lg bg-black px-5 py-3 font-semibold text-white"
        >
            + Buat Laporan
        </a>

    </div>


    @if (session('status'))

        <div class="mt-6 rounded-lg bg-green-50 p-4 text-green-700">
            {{ session('status') }}
        </div>

    @endif


    <div class="mt-8 overflow-hidden rounded-xl bg-white shadow">

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-6 py-4 text-left">
                        Fasilitas
                    </th>

                    <th class="px-6 py-4 text-left">
                        Kategori
                    </th>

                    <th class="px-6 py-4 text-left">
                        Status
                    </th>

                    <th class="px-6 py-4 text-left">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse ($reports as $report)

                    <tr class="border-t">

                        <td class="px-6 py-4">
                            {{ $report->facility->name }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $report->category }}
                        </td>

                        <td class="px-6 py-4">

                            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm">
                                {{ strtoupper($report->status) }}
                            </span>

                        </td>

                        <td class="px-6 py-4">

                            <a
                                href="{{ route(
                                    'pengguna.reports.show',
                                    $report
                                ) }}"
                                class="font-semibold underline"
                            >
                                Detail
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="4"
                            class="px-6 py-10 text-center text-gray-500"
                        >
                            Belum ada laporan.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection