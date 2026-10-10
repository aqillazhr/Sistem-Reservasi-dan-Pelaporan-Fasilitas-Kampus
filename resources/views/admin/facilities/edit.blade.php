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
            padding: 40px 8%;
        }

        .back-title {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .back {
            color: #54269a;
            font-size: 42px;
            text-decoration: none;
            line-height: 1;
        }

        h1 {
            margin: 0;
            font-size: 30px;
            color: #321750;
        }

        .subtitle {
            margin: 8px 0 0;
            color: #82718f;
            font-size: 14px;
        }

        .form-container {
            max-width: 900px;
            padding: 30px;
            border: 1px solid #dfc9f4;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 16px rgba(65, 34, 91, 0.06);
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #3c2850;
            font-size: 15px;
            font-weight: 600;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d8c5eb;
            border-radius: 8px;
            background: #fdfbff;
            color: #321750;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #8752bf;
            box-shadow: 0 0 0 3px rgba(135, 82, 191, 0.1);
        }

        textarea {
            min-height: 120px;
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
            width: 240px;
            max-width: 100%;
            height: 160px;
            margin-top: 10px;
            border: 1px solid #dfc9f4;
            border-radius: 8px;
            background: #faf7ff;
            object-fit: cover;
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
            padding: 10px;
            background: #fdfbff;
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
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee5f5;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 20px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-cancel {
            border-color: #d8c5eb;
            background: white;
            color: #68438c;
        }

        .btn-save {
            background: #7542b5;
            color: white;
        }

        .btn-save:hover {
            background: #603195;
        }

        @media (max-width: 600px) {
            .page {
                padding: 24px 16px;
            }

            .form-container {
                padding: 20px;
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
            <a href="{{ route('admin.facilities.index') }}" class="back" aria-label="Kembali">&larr;</a>

            <div>
                <h1>Edit Fasilitas</h1>
                <p class="subtitle">
                    Perbarui informasi fasilitas kampus.
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
