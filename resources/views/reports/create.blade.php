@extends('layouts.dashboard')

@section('title', 'Lapor Kerusakan')

@section('content')

<style>
    .report-page {
        max-width: 1050px;
        margin: 0 auto;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .report-title {
        margin: 0;
        font-size: 30px;
        font-weight: 700;
        color: #260f45;
    }

    .draft-link {
        color: #260f45;
        font-size: 13px;
        font-weight: 600;
        text-decoration: underline;
    }

    .report-card {
        background: #ffffff;
        border: 1px solid #d5bbfb;
        border-radius: 8px;
        padding: 24px;
    }

    .report-form {
        display: flex;
        flex-direction: column;
        gap: 17px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #260f45;
    }

    .form-control {
        width: 100%;
        min-height: 39px;
        padding: 8px 12px;
        border: 1px solid #bd93f8;
        border-radius: 5px;
        background: #ffffff;
        color: #260f45;
        font-family: 'Sora', sans-serif;
        font-size: 12px;
        outline: none;
    }

    .form-control:focus {
        border-color: #9747ff;
        box-shadow: 0 0 0 2px rgba(151, 71, 255, .1);
    }

    textarea.form-control {
        min-height: 78px;
        resize: vertical;
    }

    .photo-upload {
        min-height: 82px;
        border: 1px solid #bd93f8;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
        background: #ffffff;
    }

    .photo-upload:hover {
        background: #fbf7ff;
    }

    .photo-upload input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .photo-upload-content {
        text-align: center;
        color: #8b8b8b;
        font-size: 11px;
    }

    .photo-icon {
        font-size: 24px;
        margin-bottom: 3px;
    }

    .selected-files {
        margin-top: 5px;
        font-size: 11px;
        color: #6c5a7c;
    }

    .photo-upload-info {
        margin-top: 5px;
        color: #9a8ba5;
        font-size: 10px;
    }

    .photo-error {
        display: none;
        margin-top: 8px;
        color: #b42318;
        font-size: 12px;
        font-weight: 600;
    }

    .photo-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 12px;
    }

    .photo-item {
        position: relative;
        width: 140px;
        height: 105px;
        border: 1px solid #bd93f8;
        border-radius: 7px;
        overflow: hidden;
        background: #f7efff;
    }

    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-remove {
        position: absolute;
        top: 5px;
        right: 5px;

        width: 24px;
        height: 24px;

        border: none;
        border-radius: 50%;

        background: #ffffff;
        color: #a12626;

        font-size: 16px;
        font-weight: 700;

        cursor: pointer;

        box-shadow: 0 1px 5px rgba(0, 0, 0, .25);
    }

    .photo-remove:hover {
        background: #ffdede;
    }

    .error-box {
        margin-bottom: 18px;
        padding: 12px 15px;
        border-radius: 7px;
        background: #fff1f1;
        border: 1px solid #f3aaaa;
        color: #a12626;
        font-size: 12px;
    }

    .button-row {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 5px;
    }

    .btn {
        border: 1px solid #9747ff;
        border-radius: 5px;
        padding: 8px 14px;
        font-family: 'Sora', sans-serif;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-draft {
        background: #d5bbfb;
        color: #260f45;
    }

    .btn-preview {
        background: #9747ff;
        color: #ffffff;
    }
</style>

<div class="report-page">

    <div class="report-header">

        <h1 class="report-title">
            Lapor Kerusakan
        </h1>

        <a
            href="{{ route('pengguna.reports.drafts') }}"
            class="draft-link"
        >
            Lihat Draft Laporan
        </a>

    </div>


    @if ($errors->any())

        <div class="error-box">

            <ul style="margin: 0; padding-left: 18px;">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="report-card">

        <form
            action="{{ route('pengguna.reports.store-draft') }}"
            method="POST"
            enctype="multipart/form-data"
            class="report-form"
        >

            @csrf


            {{-- FAKULTAS --}}

            <div class="form-group">

                <label
                    for="faculty_filter"
                    class="form-label"
                >
                    Fakultas
                </label>

                <select
                    id="faculty_filter"
                    class="form-control"
                    required
                >

                    <option value="">
                        Pilih Fakultas
                    </option>

                    {{-- FAKULTAS / TINGKAT FASILITAS --}}
                    <option value="__UNIVERSITAS__">
                        Non-Fakultas
                    </option>

                    @foreach (
                        $facilities
                            ->pluck('location.fakultas')
                            ->filter()
                            ->unique()
                            ->sort()
                        as $faculty
                    )

                        <option value="{{ $faculty }}">
                            {{ $faculty }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- TIPE FASILITAS --}}

            <div class="form-group">

                <label
                    for="type_filter"
                    class="form-label"
                >
                    Tipe Fasilitas
                </label>

                <select
                    id="type_filter"
                    class="form-control"
                    disabled
                    required
                >

                    <option value="">
                        Pilih tipe fasilitas
                    </option>

                </select>

            </div>


            {{-- FASILITAS --}}

            <div class="form-group">

                <label
                    for="facility_id"
                    class="form-label"
                >
                    Fasilitas
                </label>

                <select
                    name="facility_id"
                    id="facility_id"
                    class="form-control"
                    disabled
                    required
                >

                    <option value="">
                        Pilih fasilitas
                    </option>

                </select>

            </div>


            {{-- KATEGORI --}}

            <div class="form-group">

                <label
                    for="category"
                    class="form-label"
                >
                    Kategori Kerusakan
                </label>

                <select
                    name="category"
                    id="category"
                    class="form-control"
                    required
                >

                    <option value="">
                        Pilih kategori kerusakan
                    </option>

                    <option value="Komputer">
                        Komputer
                    </option>

                    <option value="Jaringan">
                        Jaringan
                    </option>

                    <option value="Listrik">
                        Listrik
                    </option>

                    <option value="AC">
                        AC
                    </option>

                    <option value="Furnitur">
                        Furnitur
                    </option>

                    <option value="Bangunan">
                        Bangunan
                    </option>

                    <option value="Kebersihan">
                        Kebersihan
                    </option>

                    <option value="Lainnya">
                        Lainnya
                    </option>

                </select>

            </div>


            {{-- DESKRIPSI --}}

            <div class="form-group">

                <label
                    for="description"
                    class="form-label"
                >
                    Deskripsi Kerusakan
                </label>

                <textarea
                    name="description"
                    id="description"
                    class="form-control"
                    placeholder="Masukkan deskripsi kerusakan"
                >{{ old('description') }}</textarea>

            </div>


           {{-- FOTO --}}
            <div class="form-group">

                <label class="form-label">
                    Foto
                </label>

                <div
                    class="photo-upload"
                    id="photo-upload-box"
                >

                    <div class="photo-upload-content">

                        <div class="photo-icon">
                            📷
                        </div>

                        <div>
                            Tambahkan foto
                        </div>

                        <div class="photo-upload-info">
                            Maksimal 3 foto, maksimal 4 MB per foto
                        </div>

                    </div>

                    <input
                        type="file"
                        name="photos[]"
                        id="photos"
                        multiple
                        accept="image/*"
                    >

                </div>


                {{-- PESAN ERROR FOTO --}}

                <p
                    id="photo-error"
                    class="photo-error"
                ></p>


                {{-- PREVIEW FOTO --}}

                <div
                    id="photo-preview"
                    class="photo-preview"
                ></div>

            </div>


            {{-- BUTTON --}}

            <div class="button-row">

                <button
                    type="submit"
                    name="action"
                    value="save"
                    class="btn btn-draft"
                >
                    Simpan draft
                </button>

                <button
                    type="submit"
                    name="action"
                    value="preview"
                    class="btn btn-preview"
                >
                    Pratinjau laporan
                </button>

            </div>

        </form>

    </div>

</div>


<script>

const facilities = @json($facilities);

const facultySelect =
    document.getElementById('faculty_filter');

const typeSelect =
    document.getElementById('type_filter');

const facilitySelect =
    document.getElementById('facility_id');

const photoInput =
    document.getElementById('photos');

const photoPreview =
    document.getElementById('photo-preview');

const photoError =
    document.getElementById('photo-error');

const photoUploadBox =
    document.getElementById('photo-upload-box');

let selectedPhotoFiles = [];

const MAX_PHOTOS = 3;
const MAX_FILE_SIZE = 4 * 1024 * 1024;

facultySelect.addEventListener('change', function () {

    const selectedFaculty = this.value;

    typeSelect.innerHTML =
        '<option value="">Pilih tipe fasilitas</option>';

    facilitySelect.innerHTML =
        '<option value="">Pilih fasilitas</option>';

    typeSelect.disabled = true;
    facilitySelect.disabled = true;


    if (!selectedFaculty) {
        return;
    }


    const filteredFacilities =
        facilities.filter(facility => {

            if (!facility.location) {
                return false;
            }


            // Fasilitas tingkat universitas
            if (selectedFaculty === '__UNIVERSITAS__') {

                return facility.location.scope_level === 'universitas';

            }


            // Fasilitas tingkat fakultas
            return (
                facility.location.scope_level === 'fakultas' &&
                facility.location.fakultas === selectedFaculty
            );

        });


    const types = [
        ...new Map(
            filteredFacilities
                .filter(facility => facility.type)
                .map(facility => [
                    facility.type.id,
                    facility.type.name
                ])
        ).entries()
    ];


    types.forEach(([id, name]) => {

        const option =
            document.createElement('option');

        option.value = id;
        option.textContent = name;

        typeSelect.appendChild(option);

    });


    typeSelect.disabled = false;

});


typeSelect.addEventListener('change', function () {

    const selectedFaculty =
        facultySelect.value;

    const selectedType =
        this.value;


    facilitySelect.innerHTML =
        '<option value="">Pilih fasilitas</option>';

    facilitySelect.disabled = true;


    if (!selectedType) {
        return;
    }


    const filteredFacilities =
        facilities.filter(facility => {

            if (!facility.location) {
                return false;
            }


            const sameType =
                facility.type &&
                String(facility.type.id) ===
                    String(selectedType);


            if (!sameType) {
                return false;
            }


            // Tingkat universitas
            if (selectedFaculty === '__UNIVERSITAS__') {

                return (
                    facility.location.scope_level ===
                    'universitas'
                );

            }


            // Tingkat fakultas
            return (
                facility.location.scope_level ===
                    'fakultas' &&
                facility.location.fakultas ===
                    selectedFaculty
            );

        });


    filteredFacilities.forEach(facility => {

        const option =
            document.createElement('option');

        option.value =
            facility.id;

        option.textContent =
            facility.name;

        facilitySelect.appendChild(option);

    });


    facilitySelect.disabled = false;

});


photoUploadBox.addEventListener('click', function () {
    photoInput.click();
});


photoInput.addEventListener('click', function (event) {
    event.stopPropagation();
});


photoInput.addEventListener('change', function () {

    const newFiles =
        Array.from(this.files);

    photoError.style.display = 'none';
    photoError.textContent = '';

    for (const file of newFiles) {

        // Cek apakah jumlah foto sudah mencapai batas maksimal.
        if (
            selectedPhotoFiles.length
            >= MAX_PHOTOS
        ) {

            showPhotoError(
                'Maksimal 3 foto yang dapat diupload.'
            );
            break;
        }

        //Mengecek apakah file berupa gambar
        if (!file.type.startsWith('image/')) {

            showPhotoError(
                'File "' +
                file.name +
                '" bukan merupakan gambar.'
            );

            continue;
        }

        //Mengecek ukuran file 4 MB
        if (file.size > MAX_FILE_SIZE) {

            showPhotoError(
                'Foto "' +
                file.name +
                '" lebih dari 4 MB.'
            );

            continue;
        }

        selectedPhotoFiles.push(file);
    }


    syncPhotoInput();
    renderPhotoPreview();

    photoInput.value = '';

});


function renderPhotoPreview() {

    photoPreview.innerHTML = '';

    selectedPhotoFiles.forEach(
        function (file, index) {

            const reader =
                new FileReader();

            reader.onload =
                function (event) {

                    const photoItem =
                        document.createElement('div');

                    photoItem.className =
                        'photo-item';


                    photoItem.innerHTML = `

                        <img
                            src="${event.target.result}"
                            alt="Preview foto"
                        >

                        <button
                            type="button"
                            class="photo-remove"
                            data-index="${index}"
                        >
                            ×
                        </button>

                    `;

                    photoPreview.appendChild(
                        photoItem
                    );

                };

            reader.readAsDataURL(file);

        }
    );
}


photoPreview.addEventListener(
    'click',
    function (event) {

        if (
            event.target.classList.contains(
                'photo-remove'
            )
        ) {

            const index =
                Number(
                    event.target.dataset.index
                );


            selectedPhotoFiles.splice(
                index,
                1
            );


            syncPhotoInput();
            renderPhotoPreview();

        }

    }
);


function syncPhotoInput() {

    const dataTransfer =
        new DataTransfer();


    selectedPhotoFiles.forEach(
        function (file) {

            dataTransfer.items.add(file);

        }
    );


    photoInput.files =
        dataTransfer.files;
}


function showPhotoError(message) {

    photoError.textContent =
        message;

    photoError.style.display =
        'block';
}

</script>

@endsection