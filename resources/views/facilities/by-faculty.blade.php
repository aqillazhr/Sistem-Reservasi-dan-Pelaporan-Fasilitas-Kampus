@extends('layouts.dashboard')

@section('title', $faculty)

@push('styles')
    <style>
        .faculty-page {
            padding: 40px;
        }

        .faculty-page h1 {
            color: #54269a;
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

        .faculty-page {
            padding: 20px 40px 40px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            text-decoration: none;
            margin-bottom: 5px;
        }

        .back-button span {
            width: 13px;
            height: 13px;
            border-left: 4px solid #54269a;
            border-bottom: 4px solid #54269a;
            transform: rotate(45deg);
        }
    </style>
@endpush

@section('content')

    <div class="faculty-page">
        <a href="{{ route('pengguna.dashboard') }}" class="back-button">
            <span></span>
        </a>

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

                <a
                    href="{{ route('facilities.show', [
                        'facility' => $facility,
                        'from' => url()->full(),
                    ]) }}">
                    Lihat Detail
                </a>

            </div>

        @empty

            <p>
                Belum ada fasilitas di fakultas ini.
            </p>
        @endforelse

    </div>

@endsection
