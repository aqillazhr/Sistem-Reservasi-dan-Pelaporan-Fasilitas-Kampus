<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fasilitas</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #faf7ff;
            color: #321750;
            position: relative;
            overflow-x: hidden;
        }

        body::after {
            content: "";
            position: absolute;
            width: 420px;
            height: 650px;
            right: -95px;
            bottom: -110px;
            background: url('{{ asset('images/graphic.png') }}') no-repeat center / contain;
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
        }

        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            padding: 40px 44px;
        }

        /* Header halaman */
        .back-title {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 34px;
        }

        /* Tombol kembali */
        .back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            border: 1px solid #e3d3f3;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.88);
            color: #7542b5;
            font-size: 30px;
            line-height: 1;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(75, 43, 105, 0.06);
            transition:
                background-color 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }

        .back:hover {
            background: #f0e5fc;
            border-color: #c7a7e8;
            transform: translateX(-2px);
        }

        .back-icon {
            width: 23px;
            height: 23px;
            transition: transform 0.2s ease;
        }

        /* Area judul */
        .heading-copy {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .heading-eyebrow {
            margin-bottom: 6px;
            color: #8752bf;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.7px;
            line-height: 1.4;
        }

        h1 {
            margin: 0;
            color: #321750;
            font-size: 34px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.8px;
        }

        .subtitle {
            margin: 8px 0 0;
            color: #81718f;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-container {
            position: relative;
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 36px 38px;
            border: 1px solid rgba(188, 151, 224, 0.35);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow:
                0 12px 35px rgba(75, 43, 105, 0.07),
                0 2px 8px rgba(75, 43, 105, 0.03);
            backdrop-filter: blur(8px);
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            color: #39234f;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.1px;
        }

        input,
        select,
        textarea {
            display: block;
            width: 100%;
            min-height: 48px;
            padding: 13px 15px;
            border: 1px solid #e2d5f0;
            border-radius: 10px;
            background: #fcfaff;
            color: #38204f;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.5;
            outline: none;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.2s ease;
        }

        input:hover,
        select:hover,
        textarea:hover {
            border-color: #c4a5e5;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #8752bf;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(135, 82, 191, 0.11);
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        .field-note {
            margin-top: 7px;
            color: #887a95;
            font-size: 12px;
            line-height: 1.5;
        }

        /* Foto fasilitas saat ini */
        .current-photo {
            display: block;
            width: 280px;
            max-width: 100%;
            height: 190px;
            margin-top: 12px;
            border: 1px solid #e9def5;
            border-radius: 14px;
            background: #f7f1fd;
            object-fit: cover;
            box-shadow: 0 6px 18px rgba(65, 34, 91, 0.08);
        }

        .photo-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 240px;
            max-width: 100%;
            height: 130px;
            margin-top: 10px;
            border: 1px dashed #cdb5e7;
            border-radius: 8px;
            color: #82718f;
            background: #faf7ff;
            font-size: 13px;
        }

        .photo-input {
            display: block;
            width: 100%;
            min-height: auto;
            margin-top: 14px;
            padding: 12px;
            border: 1px dashed #c7a6e8;
            border-radius: 12px;
            background: #faf6ff;
            color: #6c547f;
            font-size: 13px;
            cursor: pointer;
        }

        .photo-input::file-selector-button {
            margin-right: 14px;
            padding: 9px 14px;
            border: 1px solid #d8c0ef;
            border-radius: 8px;
            background: #eee2fb;
            color: #633b91;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .photo-input::file-selector-button:hover {
            background: #e2d0f7;
        }

        .error {
            margin-top: 6px;
            color: #b42318;
            font-size: 12px;
        }

        .form-alert {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 8px;
            background: #fff0ee;
            color: #a1261c;
            font-size: 13px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #f0e8f7;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 11px 20px;
            border: 1px solid transparent;
            border-radius: 10px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition:
                background-color 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }

        .btn-cancel {
            border-color: #dfd0ee;
            background: #ffffff;
            color: #674585;
        }

        .btn-cancel:hover {
            border-color: #bfa0df;
            background: #f8f2fd;
        }

        .btn-save {
            background: #7542b5;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(117, 66, 181, 0.16);
        }

        .btn-save:hover {
            background: #603195;
            transform: translateY(-1px);
        }

        @media (max-width: 600px) {
            .page {
                padding: 24px 16px;
            }

            .back-title {
                align-items: flex-start;
                gap: 13px;
                margin-bottom: 26px;
            }

            .back {
                width: 44px;
                height: 44px;
                border-radius: 13px;
                font-size: 26px;
            }

            .heading-eyebrow {
                font-size: 10px;
                letter-spacing: 1.3px;
            }

            h1 {
                font-size: 27px;
                letter-spacing: -0.5px;
            }

            .subtitle {
                font-size: 13px;
            }

            .form-container {
                padding: 22px 18px;
                border-radius: 15px;
            }

            .buttons {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="page">

        <div class="back-title">
            <a href="{{ route('admin.facilities.index') }}" class="back" aria-label="Kembali ke daftar fasilitas"
                title="Kembali ke daftar fasilitas">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </a>

            <div class="heading-copy">
                <span class="heading-eyebrow">MANAJEMEN FASILITAS</span>

                <h1>Edit Fasilitas</h1>

                <p class="subtitle">
                    Perbarui informasi dan detail fasilitas kampus.
                </p>
            </div>
        </div>

        <div class="form-container">

            @if ($errors->any())
                <div class="form-alert">
                    Ada data yang belum valid. Periksa kembali kolom yang ditandai.
                </div>
            @endif

            <form action="{{ route('admin.facilities.update', $facility) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Nama fasilitas --}}
                <div class="form-group">
                    <label for="name">Nama Fasilitas</label>

                    <input type="text" id="name" name="name" value="{{ old('name', $facility->name) }}"
                        required maxlength="255">

                    @error('name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tipe fasilitas --}}
                <div class="form-group">
                    <label for="type_id">Tipe Fasilitas</label>

                    <select id="type_id" name="type_id" required>
                        <option value="">Pilih tipe fasilitas</option>

                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected((string) old('type_id', $facility->type_id) === (string) $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('type_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Lokasi --}}
                <div class="form-group">
                    <label for="location_id">Lokasi Fasilitas</label>

                    <select id="location_id" name="location_id" required>
                        <option value="">Pilih lokasi fasilitas</option>

                        @foreach ($locations as $location)
                            @php
                                $locationParts = [];

                                if ($location->scope_level === 'universitas') {
                                    $locationParts[] = 'Universitas';
                                }

                                if ($location->fakultas) {
                                    $locationParts[] = $location->fakultas;
                                }

                                if ($location->prodi) {
                                    $locationParts[] = $location->prodi;
                                }

                                if ($location->gedung) {
                                    $locationParts[] = 'Gedung ' . $location->gedung;
                                }

                                if ($location->ruangan) {
                                    $locationParts[] = 'Ruang ' . $location->ruangan;
                                }

                                $locationLabel = count($locationParts)
                                    ? implode(' — ', $locationParts)
                                    : 'Lokasi #' . $location->id;
                            @endphp

                            <option value="{{ $location->id }}" @selected((string) old('location_id', $facility->location_id) === (string) $location->id)>
                                {{ $locationLabel }}
                            </option>
                        @endforeach
                    </select>

                    <p class="field-note">
                        Pilih lokasi yang sesuai dengan fakultas, gedung,
                        dan ruangan fasilitas. Data lokasi berasal dari database.
                    </p>

                    @error('location_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kapasitas --}}
                <div class="form-group">
                    <label for="capacity">Kapasitas</label>

                    <input type="number" id="capacity" name="capacity"
                        value="{{ old('capacity', $facility->capacity) }}" min="1" required>

                    @error('capacity')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="form-group">
                    <label for="description">Deskripsi</label>

                    <textarea id="description" name="description">{{ old('description', $facility->description) }}</textarea>

                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Foto fasilitas --}}
                <div class="form-group">
                    <label for="photo">Foto Fasilitas</label>

                    @php
                        $currentPhoto = $facility->photos->first();
                    @endphp

                    @if ($currentPhoto)
                        <p class="field-note">Foto yang tersimpan saat ini:</p>

                        <img src="{{ asset('storage/' . $currentPhoto->file_path) }}" alt="Foto {{ $facility->name }}"
                            class="current-photo">
                    @else
                        <div class="photo-empty">
                            Belum ada foto fasilitas
                        </div>
                    @endif

                    <input type="file" id="photo" name="photo" class="photo-input"
                        accept="image/jpeg,image/png,image/webp">

                    <p class="field-note">
                        Pilih foto baru jika ingin mengganti foto yang sekarang.
                        Kosongkan jika ingin mempertahankan foto yang ada.
                        Format JPG, JPEG, atau PNG. Maksimal 5 MB.
                    </p>

                    @error('photo')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="buttons">
                    <a href="{{ route('admin.facilities.index') }}" class="btn btn-cancel">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-save">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</body>

</html>
