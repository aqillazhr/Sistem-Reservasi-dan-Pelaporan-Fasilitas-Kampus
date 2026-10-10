<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Fasilitas</title>

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

        /* Elemen grafis ungu di pojok kanan bawah */
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
            transition: all 0.2s ease;
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

        .heading-copy {
            display: flex;
            flex-direction: column;
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

        /* Card form */
        .form-container {
            position: relative;
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 36px 38px;
            border: 1px solid rgba(188, 151, 224, 0.45);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow:
                0 12px 35px rgba(75, 43, 105, 0.07),
                0 2px 8px rgba(75, 43, 105, 0.03);
            backdrop-filter: blur(8px);
        }

        .form-alert {
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #f4c6c1;
            border-radius: 9px;
            background: #fff0ee;
            color: #a1261c;
            font-size: 13px;
        }

        /* Kolom form */
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
            min-width: 0;
            min-height: 48px;
            padding: 12px 15px;
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
            background: #fff;
            box-shadow: 0 0 0 4px rgba(135, 82, 191, 0.11);
        }

        input::placeholder,
        textarea::placeholder {
            color: #a79bb3;
        }

        /* Dropdown */
        select {
            appearance: none;
            padding-right: 42px;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%237542b5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }

        select:disabled {
            background-color: #f1eef4;
            color: #aaa;
            cursor: not-allowed;
        }

        /* Deskripsi */
        textarea {
            min-height: 130px;
            resize: vertical;
        }

        /* Petunjuk di bawah input */
        .photo-note,
        .field-note {
            margin-top: 7px;
            color: #887a95;
            font-size: 12px;
            line-height: 1.6;
        }

        /* Upload foto */
        .file-input {
            display: block;
            width: 100%;
            min-height: auto;
            margin-top: 4px;
            padding: 12px;
            border: 1px dashed #c7a6e8;
            border-radius: 12px;
            background: #faf6ff;
            color: #6c547f;
            font-size: 13px;
            cursor: pointer;
        }

        .file-input::file-selector-button {
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

        .file-input::file-selector-button:hover {
            background: #e2d0f7;
        }

        /* Pesan validasi */
        .error {
            margin-top: 6px;
            color: #b42318;
            font-size: 12px;
            line-height: 1.5;
        }

        /* Tombol bawah form */
        .buttons {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: wrap;
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
            background: #fff;
            color: #674585;
        }

        .btn-cancel:hover {
            border-color: #bfa0df;
            background: #f8f2fd;
        }

        .btn-save {
            background: #7542b5;
            color: #fff;
            box-shadow: 0 4px 10px rgba(117, 66, 181, 0.16);
        }

        .btn-save:hover {
            background: #603195;
            transform: translateY(-1px);
        }

        /* Tampilan layar kecil */
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

            h1 {
                font-size: 27px;
                letter-spacing: -0.5px;
            }

            .heading-eyebrow {
                font-size: 10px;
                letter-spacing: 1.3px;
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

                <h1>Tambah Fasilitas</h1>

                <p class="subtitle">
                    Lengkapi informasi fasilitas yang ingin ditambahkan ke sistem.
                </p>
            </div>
        </div>

        <div class="form-container">

            @if ($errors->any())
                <div class="form-alert">
                    Ada data yang belum valid. Periksa kembali kolom yang ditandai.
                </div>
            @endif

            <form action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Fakultas --}}
                <div class="form-group">
                    <label for="fakultas">Fakultas</label>

                    <select name="fakultas" id="fakultas" required>
                        <option value="">Pilih lingkup fasilitas</option>

                        <option value="__UNIVERSITAS__" {{ old('fakultas') === '__UNIVERSITAS__' ? 'selected' : '' }}>
                            Fasilitas Umum Universitas
                        </option>

                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty }}" {{ old('fakultas') == $faculty ? 'selected' : '' }}>
                                {{ $faculty }}
                            </option>
                        @endforeach
                    </select>

                    @error('fakultas')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Program Studi --}}
                <div class="form-group">
                    <label for="prodi">Program Studi</label>

                    <select name="prodi" id="prodi" disabled>
                        <option value="">Pilih program studi</option>
                    </select>

                    @error('prodi')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tipe Fasilitas --}}
                <div class="form-group">
                    <label for="type_id">Tipe Fasilitas</label>

                    <select name="type_id" id="type_id" required>
                        <option value="">Pilih tipe fasilitas</option>

                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" data-name="{{ strtolower($type->name) }}"
                                {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('type_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Gedung --}}
                <div class="form-group">
                    <label for="gedung">Gedung</label>

                    <select name="gedung" id="gedung" disabled>
                        <option value="">Pilih fakultas terlebih dahulu</option>
                    </select>

                    @error('gedung')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nama Fasilitas --}}
                <div class="form-group">
                    <label for="name">Fasilitas</label>

                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        placeholder="Masukkan fasilitas yang ingin ditambahkan" required>

                    @error('name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kapasitas --}}
                <div class="form-group">
                    <label for="capacity" id="capacity-label">
                        Kapasitas Ruangan (Opsional)
                    </label>

                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" min="1"
                        placeholder="Masukkan kapasitas fasilitas">

                    <p class="field-note">
                        Wajib diisi untuk tipe Ruang Kelas, Aula, Laboratorium, dan Lapangan.
                        Untuk tipe Alat, kapasitas bersifat opsional.
                    </p>
                    @error('capacity')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Lokasi Ruangan --}}
                <div class="form-group">
                    <label for="ruangan">Lokasi Ruangan</label>

                    <input type="text" name="ruangan" id="ruangan" value="{{ old('ruangan') }}"
                        placeholder="Masukkan lokasi fasilitas yang ingin ditambahkan">

                    @error('ruangan')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="form-group">
                    <label for="description">Deskripsi</label>

                    <textarea name="description" id="description" placeholder="Masukkan deskripsi dari fasilitas yang ingin ditambahkan">{{ old('description') }}</textarea>

                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Foto --}}
                <div class="form-group">
                    <label for="photo">Foto Fasilitas</label>

                    <input type="file" name="photo" id="photo" class="file-input" accept="image/*">

                    <div class="photo-note">
                        Format gambar yang didukung: JPG, JPEG, PNG, dan WEBP. Maksimal 5 MB.
                    </div>

                    @error('photo')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="buttons">

                    <a href="{{ url()->previous() }}" class="btn btn-cancel">
                        Batalkan
                    </a>

                    <button type="submit" class="btn btn-save">
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>

    <script>
        const fakultas = document.getElementById('fakultas');
        const prodi = document.getElementById('prodi');
        const tipe = document.getElementById('type_id');
        const capacity = document.getElementById('capacity');
        const capacityLabel = document.getElementById('capacity-label');
        const gedung = document.getElementById('gedung');

        const programsByFaculty = @json($programsByFaculty);
        const buildingsByFaculty = @json($buildingsByFaculty);
        const universityBuildings = @json($universityBuildings);

        // Nilai lama dipakai kembali jika validasi form gagal.
        const oldProdi = @json(old('prodi', ''));
        const oldGedung = @json(old('gedung', ''));

        function fillSelect(select, placeholder, options, selectedValue = '') {
            select.innerHTML = '';

            const firstOption = document.createElement('option');
            firstOption.value = '';
            firstOption.textContent = placeholder;
            select.appendChild(firstOption);

            options.forEach(function(value) {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });

            if (options.includes(selectedValue)) {
                select.value = selectedValue;
            }
        }

        function updateLocationOptions(useOldValues = false) {
            const selectedFaculty = fakultas.value;

            // Reset dropdown ketika fakultas berubah.
            fillSelect(prodi, 'Pilih program studi (opsional)', [], '');
            fillSelect(gedung, 'Pilih gedung', [], '');

            if (selectedFaculty === '__UNIVERSITAS__') {
                // Fasilitas tingkat universitas tidak memiliki fakultas/prodi.
                prodi.disabled = true;
                fillSelect(prodi, 'Tidak diperlukan untuk fasilitas universitas', [], '');

                gedung.disabled = false;

                fillSelect(
                    gedung,
                    'Pilih gedung universitas',
                    universityBuildings,
                    useOldValues ? oldGedung : ''
                );

            } else if (selectedFaculty) {
                // Fasilitas tingkat fakultas.
                prodi.disabled = false;

                const programs = programsByFaculty[selectedFaculty] ?? [];
                const buildings = buildingsByFaculty[selectedFaculty] ?? [];

                fillSelect(
                    prodi,
                    'Pilih program studi (opsional)',
                    programs,
                    useOldValues ? oldProdi : ''
                );

                fillSelect(
                    gedung,
                    'Pilih gedung fakultas',
                    buildings,
                    useOldValues ? oldGedung : ''
                );

                gedung.disabled = buildings.length === 0;

            } else {
                // Fakultas belum dipilih.
                prodi.disabled = true;
                gedung.disabled = true;

                fillSelect(prodi, 'Pilih fakultas terlebih dahulu', [], '');
                fillSelect(gedung, 'Pilih fakultas terlebih dahulu', [], '');
            }

            updateBuildingByType();
        }

        function updateBuildingByType() {
            const selectedOption = tipe.options[tipe.selectedIndex];
            const typeName = selectedOption?.dataset.name || '';

            // Lapangan tidak memerlukan gedung.
            if (typeName.includes('lapangan')) {
                gedung.value = '';
                gedung.disabled = true;
            }
        }

        function updateCapacityRequirement() {
            const selectedOption = tipe.options[tipe.selectedIndex];
            const typeName = (selectedOption?.dataset.name || '')
                .trim()
                .toLowerCase();

            const requiredTypes = [
                'ruang kelas',
                'aula',
                'laboratorium',
                'lapangan'
            ];

            const isRequired = requiredTypes.includes(typeName);

            capacity.required = isRequired;

            capacityLabel.textContent = isRequired ?
                'Kapasitas Ruangan *' :
                'Kapasitas Ruangan (Opsional)';
        }

        fakultas.addEventListener('change', function() {
            // Pemilihan fakultas baru mereset prodi dan gedung.
            updateLocationOptions(false);
        });

        tipe.addEventListener('change', updateBuildingByType);
        tipe.addEventListener('change', updateCapacityRequirement);

        // Pulihkan nilai lama ketika form dikembalikan oleh validasi.
        updateLocationOptions(true);
        updateCapacityRequirement();
    </script>

</body>

</html>
