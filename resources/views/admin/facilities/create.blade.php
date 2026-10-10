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
            font-family: 'Sora', Arial, sans-serif;
            background: #fbf7ff;
            color: #111;
        }

        .page {
            min-height: 100vh;
            padding: 24px 38px 40px;
        }

        .back-title {
            display: flex;
            align-items: center;
            gap: 14px;
            width: min(100%, 656px);
            margin: 0 0 8px 30px;
        }

        .back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            text-decoration: none;
            color: #111;
            line-height: 1;
        }

        h1 {
            font-size: 23px;
            font-weight: 700;
            margin: 0;
        }

        .form-container {
            width: min(100%, 656px);
            margin-left: 30px;
            background: #fff;
            border: 1px solid #bd93f8;
            border-radius: 8px;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        input,
        select,
        textarea {
            display: block;
            width: 100%;
            min-width: 0;
            border: 1px solid #bd93f8;
            border-radius: 7px;
            background-color: #fbf7ff;
            padding: 8px 14px;
            font-family: inherit;
            font-size: 13px;
            color: #260f45;
            outline: none;
        }

        input:not([type="file"]),
        select {
            height: 36px;
        }

        input::placeholder,
        textarea::placeholder {
            color: #b5aebd;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #7133d1;
            box-shadow: 0 0 0 2px rgba(189, 147, 248, 0.15);
        }

        select {
            appearance: none;
            cursor: pointer;
            padding-right: 38px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23111' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m9 18 6-6-6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 12px;
        }

        select:disabled {
            background-color: #eeeeee;
            color: #aaa;
            cursor: not-allowed;
        }

        textarea {
            min-height: 36px;
            height: 36px;
            resize: vertical;
        }

        .file-input {
            min-height: 36px;
            padding: 5px 8px;
            background: #fbf7ff;
            font-size: 13px;
        }

        .photo-note {
            margin-top: 6px;
            font-size: 12px;
            color: #81778d;
            line-height: 1.5;
        }

        .error {
            color: #d32f2f;
            font-size: 12px;
            margin-top: 5px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #bd93f8;
            border-radius: 7px;
            padding: 9px 14px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            line-height: 1.2;
        }

        .btn-cancel {
            background: #fff;
            color: #111;
        }

        .btn-save {
            background: #bd93f8;
            color: #111;
            border-color: #bd93f8;
        }

        .btn-save:hover {
            background: #a979ed;
        }

        @media (max-width: 760px) {
            .page {
                padding: 20px 16px;
            }

            .back-title,
            .form-container {
                width: 100%;
                margin-left: 0;
            }

            .form-container {
                padding: 16px;
            }

            .buttons {
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <div class="back-title">
            <a href="{{ url()->previous() }}" class="back">←</a>
            <h1>Tambah Fasilitas</h1>
        </div>

        <div class="form-container">

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
                        Format gambar yang didukung: JPG, JPEG, PNG. Maksimal 5 MB.
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
