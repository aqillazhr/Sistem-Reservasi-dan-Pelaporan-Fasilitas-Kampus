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
            color: #111;
        }

        .page {
            min-height: 100vh;
            padding: 50px 8%;
        }

        .back-title {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 25px;
        }

        .back {
            font-size: 55px;
            text-decoration: none;
            color: #111;
            line-height: 1;
        }

        h1 {
            font-size: 42px;
            margin: 0;
        }

        .form-container {
            background: white;
            border: 2px solid #b47bea;
            border-radius: 10px;
            padding: 35px;
            max-width: 1100px;
        }

        .form-group {
            margin-bottom: 28px;
        }

        label {
            display: block;
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 2px solid #a56df0;
            border-radius: 8px;
            background: #fcf8ff;
            padding: 14px 20px;
            font-size: 18px;
            color: #333;
            outline: none;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #7133d1;
        }

        select {
            cursor: pointer;
        }

        select:disabled {
            background: #eeeeee;
            color: #aaa;
            cursor: not-allowed;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .file-input {
            padding: 12px;
            background: #fcf8ff;
        }

        .error {
            color: #d32f2f;
            font-size: 14px;
            margin-top: 6px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            margin-top: 35px;
        }

        .btn {
            border: 2px solid #a56df0;
            border-radius: 8px;
            padding: 13px 28px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-cancel {
            background: white;
            color: #111;
        }

        .btn-save {
            background: #b47bea;
            color: #111;
            border-color: #b47bea;
        }

        .btn-save:hover {
            background: #a56de0;
        }

        .photo-note {
            margin-top: 7px;
            font-size: 14px;
            color: #777;
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

        <form
            action="{{ route('admin.facilities.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            {{-- Fakultas --}}
            <div class="form-group">
                <label for="fakultas">Fakultas</label>

                <select name="fakultas" id="fakultas">
                    <option value="">Pilih Fakultas</option>

                    @foreach($faculties as $faculty)
                        <option
                            value="{{ $faculty }}"
                            {{ old('fakultas') == $faculty ? 'selected' : '' }}
                        >
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

                    @foreach($types as $type)
                        <option
                            value="{{ $type->id }}"
                            data-name="{{ strtolower($type->name) }}"
                            {{ old('type_id') == $type->id ? 'selected' : '' }}
                        >
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

                <select name="gedung" id="gedung">
                    <option value="">Pilih Gedung</option>

                    @foreach($buildings as $building)
                        <option
                            value="{{ $building }}"
                            {{ old('gedung') == $building ? 'selected' : '' }}
                        >
                            {{ $building }}
                        </option>
                    @endforeach
                </select>

                @error('gedung')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nama Fasilitas --}}
            <div class="form-group">
                <label for="name">Fasilitas</label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    placeholder="Masukkan fasilitas yang ingin ditambahkan"
                    required
                >

                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kapasitas --}}
            <div class="form-group">
                <label for="capacity">Kapasitas Ruangan</label>

                <input
                    type="number"
                    name="capacity"
                    id="capacity"
                    value="{{ old('capacity') }}"
                    min="1"
                    placeholder="Masukkan kapasitas fasilitas yang ingin ditambahkan"
                    required
                >

                @error('capacity')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Lokasi Ruangan --}}
            <div class="form-group">
                <label for="ruangan">Lokasi Ruangan</label>

                <input
                    type="text"
                    name="ruangan"
                    id="ruangan"
                    value="{{ old('ruangan') }}"
                    placeholder="Masukkan lokasi fasilitas yang ingin ditambahkan"
                >

                @error('ruangan')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="form-group">
                <label for="description">Deskripsi</label>

                <textarea
                    name="description"
                    id="description"
                    placeholder="Masukkan deskripsi dari fasilitas yang ingin ditambahkan"
                >{{ old('description') }}</textarea>

                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Foto --}}
            <div class="form-group">
                <label for="photo">Foto Fasilitas</label>

                <input
                    type="file"
                    name="photo"
                    id="photo"
                    class="file-input"
                    accept="image/*"
                >

                <div class="photo-note">
                    Format gambar yang didukung: JPG, JPEG, PNG. Maksimal 5 MB.
                </div>

                @error('photo')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="buttons">

                <a
                    href="{{ url()->previous() }}"
                    class="btn btn-cancel"
                >
                    Batalkan
                </a>

                <button
                    type="submit"
                    class="btn btn-save"
                >
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
    const gedung = document.getElementById('gedung');

    // Program studi sementara diambil dari data
    // yang akan kita kirim dari controller.
    const programsByFaculty = @json($programsByFaculty);

    fakultas.addEventListener('change', function () {
        const selectedFaculty = this.value;

        prodi.innerHTML = '<option value="">Pilih program studi</option>';

        if (!selectedFaculty) {
            prodi.disabled = true;
            return;
        }

        prodi.disabled = false;

        const programs = programsByFaculty[selectedFaculty] ?? [];

        programs.forEach(function (program) {
            const option = document.createElement('option');

            option.value = program;
            option.textContent = program;

            prodi.appendChild(option);
        });
    });

    tipe.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const typeName = selectedOption.dataset.name || '';

        if (typeName.includes('lapangan')) {
            gedung.value = '';
            gedung.disabled = true;
        } else {
            gedung.disabled = false;
        }
    });

    // Jalankan kondisi awal jika ada old input.
    tipe.dispatchEvent(new Event('change'));
    fakultas.dispatchEvent(new Event('change'));
</script>

</body>
</html>