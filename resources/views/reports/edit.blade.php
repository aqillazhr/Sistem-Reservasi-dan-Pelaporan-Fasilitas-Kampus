@extends('layouts.dashboard')

@section('title', 'Edit Draft Laporan')

@section('content')

<style>
    .edit-page {
        max-width: 1050px;
        margin: 0 auto;
    }

    .edit-title {
        margin: 0;
        font-size: 46px;
        font-weight: 700;
        color: #501e91;
        margin-bottom: 25px;
    }

    .edit-card {
        background: #ffffff;
        border: 1px solid #bd93f8;
        border-radius: 7px;
        padding: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 17px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #501e91;
    }

    .form-control {
        width: 100%;
        min-height: 39px;
        padding: 8px 12px;
        border: 1px solid #bd93f8;
        border-radius: 5px;
        background: #ffffff;
        color: #501e91;
        font-family: 'Sora', sans-serif;
        font-size: 12px;
        outline: none;
    }

    .form-control:focus {
        border-color: #9747ff;
        box-shadow: 0 0 0 2px rgba(151, 71, 255, .1);
    }

    textarea.form-control {
        min-height: 80px;
    }

    .photo-upload {
        min-height: 100px;
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

    .photo-upload-info {
        margin-top: 5px;
        color: #9a8ba5;
        font-size: 10px;
    }

    .photo-error {
        display: none;
        color: #b42318;
        font-size: 12px;
        font-weight: 600;
        margin-top: 8px;
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
        box-shadow: 0 1px 5px rgba(0,0,0,.25);
    }

    .photo-remove:hover {
        background: #ffdede;
    }

    .button-row {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 18px;
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

    .btn-cancel {
        background: #ffffff;
        color: #260f45;
    }

    .btn-save {
        background: #9747ff;
        color: #ffffff;
    }

    .validation-message {
        color: #b42318 !important;
        font-family: 'Sora', sans-serif;
        font-size: 12px;
        font-weight: 400;
        margin-top: 5px;
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
    }

    .confirm-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(38, 15, 69, 0.45);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }

    .confirm-overlay.show {
        display: flex;
    }

    .confirm-modal {
        width: 100%;
        max-width: 350px;
        padding: 22px 24px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(38, 15, 69, 0.2);
        text-align: center;
        font-family: 'Sora', sans-serif;
    }

    .confirm-modal h3 {
        margin: 0 0 10px;
        color: #30104f;
        font-size: 16px;
        font-weight: 700;
    }

    .confirm-modal p {
        margin: 0 0 20px;
        color: #6c6474;
        font-size: 12px;
        line-height: 1.6;
    }

    .confirm-actions {
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .confirm-actions button {
        border: 1px solid #bd93f8;
        border-radius: 6px;
        padding: 8px 14px;
        font-family: 'Sora', sans-serif;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .confirm-cancel {
        background: #ffffff;
        color: #501e91;
    }

    .confirm-delete {
        background: #9747ff;
        border-color: #9747ff !important;
        color: #ffffff;
    }


</style>

<div class="edit-page">
    <h1 class="edit-title">Edit Draft Laporan</h1>

    <div class="edit-card">
        <form action="{{ route('pengguna.reports.update-draft', $report) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- FAKULTAS --}}
            <div class="form-group">
                <label for="faculty_filter" class="form-label">Fakultas</label>
                <select id="faculty_filter" class="form-control" required>
                    <option value="">Pilih Fakultas</option>
                    <option value="__UNIVERSITAS__" {{ ($report->facility->location->scope_level ?? '') === 'universitas' ? 'selected' : '' }}>Non-Fakultas</option>
                    @foreach ($facilities->pluck('location.fakultas')->filter()->unique()->sort() as $faculty)
                        <option value="{{ $faculty }}" {{ ($report->facility->location->fakultas ?? '') === $faculty ? 'selected' : '' }}>{{ $faculty }}</option>
                    @endforeach
                </select>
            </div>


            {{-- TIPE FASILITAS --}}
            <div class="form-group">
                <label for="type_filter" class="form-label">Tipe Fasilitas</label>
                <select id="type_filter" class="form-control" required>
                    <option value="">Pilih tipe fasilitas</option>
                </select>
            </div>


            {{-- FASILITAS --}}
            <div class="form-group">
                <label for="facility_id" class="form-label">Fasilitas</label>
                <select name="facility_id" id="facility_id" class="form-control" required>
                    <option value="">Pilih fasilitas</option>
                </select>
            </div>


            {{-- KATEGORI KERUSAKAN --}}
            <div class="form-group">
                <label for="category" class="form-label">Kategori Kerusakan</label>
                <select name="category" id="category" class="form-control" required>
                    @foreach (['Komputer', 'Jaringan', 'Listrik', 'AC', 'Furnitur', 'Bangunan', 'Kebersihan', 'Lainnya'] as $category)
                        <option value="{{ $category }}" {{ $report->category === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>


            {{-- DESKRIPSI KERUSAKAN --}}
            <div class="form-group">
                <label for="description" class="form-label">Deskripsi Kerusakan</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $report->description) }}</textarea>

                <div id="description-error" class="validation-message" style="display: none;">
                    Deskripsi wajib diisi jika memilih kategori kerusakan Lainnya.
                </div>

                @error('description')
                    <div class="validation-message">{{ $message }}</div>
                @enderror
            </div>


            {{-- FOTO SAAT INI --}}
            @if ($report->photos->count())
                <div class="form-group">
                    <label class="form-label">Foto Saat Ini</label>
                    <div id="existing-photo-preview" class="photo-preview">
                        @foreach ($report->photos as $photo)
                            <div class="photo-item existing-photo-item" data-photo-id="{{ $photo->id }}">
                                <img src="{{ asset( 'storage/' . $photo->file_path) }}" alt="Foto laporan">
                                <button type="button" class="photo-remove existing-photo-remove" data-photo-id="{{ $photo->id }}" title="Hapus foto">×</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div id="deleted-photo-inputs"></div>


            {{-- TAMBAH FOTO --}}
            <div class="form-group">
                <label class="form-label">Tambah Foto</label>
                <div class="photo-upload" id="photo-upload-box">
                    <div class="photo-upload-content">
                        <div class="photo-icon">📷</div>
                        <div>Tambahkan foto</div>
                        <div class="photo-upload-info">Maksimal 3 foto total, maksimal 4 MB per foto</div>
                    </div>
                    <input type="file" name="photos[]" id="photos" multiple accept="image/*">
                </div>

                <p id="photo-error" class="photo-error"></p>
                <p id="photo-count" style="margin-top: 6px; color: #76677f; font-size: 11px;"></p>
                <div id="photo-preview" class="photo-preview"></div>
            </div>

            <div class="button-row">
                <a href="{{ route('pengguna.reports.preview', $report) }}" class="btn btn-cancel">Batal</a>
                <button type="submit" class="btn btn-save">Simpan perubahan</button>
            </div>
        </form>
    </div>
</div>


{{-- KONFIRMASI HAPUS FOTO --}}
<div class="confirm-overlay" id="delete-photo-modal">
    <div class="confirm-modal">
        <h3>Hapus Foto</h3>
        <p>Foto ini akan dihapus saat anda menyimpan perubahan. Lanjutkan?</p>
        <div class="confirm-actions">
            <button type="button" class="confirm-cancel" id="cancel-delete-photo">Batal</button>
            <button type="button" class="confirm-delete" id="confirm-delete-photo">Ya, Hapus</button>
        </div>
    </div>
</div>

@php
    $facilityData = $facilities->map(function ($facility) {
        return [
            'id' => $facility->id,
            'name' => $facility->name,
            'faculty' => $facility->location->fakultas ?? '',
            'scope_level' => $facility->location->scope_level ?? '',
            'type' => $facility->type->name ?? '',
            'type_id' => $facility->type_id,
        ];
    })->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const facultySelect = document.getElementById('faculty_filter');
    const typeSelect = document.getElementById('type_filter');
    const facilitySelect = document.getElementById('facility_id');
    const facilities = @json($facilityData);

    const currentFaculty = @json(($report->facility->location->scope_level ?? '') === 'universitas' ? '__UNIVERSITAS__' : ($report->facility->location->fakultas ?? ''));
    const currentType = @json($report->facility->type->name ?? '');
    const currentFacilityId = @json( $report->facility_id);

    function populateTypes(selectedFaculty, selectedType = '') {
        typeSelect.innerHTML = `<option value="">Pilih tipe fasilitas</option>`;
        facilitySelect.innerHTML = `<option value="">Pilih fasilitas</option>`;

        if (!selectedFaculty) {
            typeSelect.disabled = true;
            facilitySelect.disabled = true;
            return;
        }

        const filteredFacilities = facilities.filter(facility => {
                if (selectedFaculty === '__UNIVERSITAS__') {
                    return facility.scope_level === 'universitas';
                }

                return facility.scope_level === 'fakultas' && facility.faculty === selectedFaculty;
        });

        const types = [...new Set(filteredFacilities.map(facility => facility.type).filter(type => type))];

        types.forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;

            if (type === selectedType) {
                option.selected = true;
            }

            typeSelect.appendChild(option);
        });

        typeSelect.disabled = false;
    }

    function populateFacilities(selectedFaculty, selectedType, selectedFacilityId = '') {
        facilitySelect.innerHTML = `<option value="">Pilih fasilitas</option>`;

        if (!selectedFaculty || !selectedType) {
            facilitySelect.disabled = true;
            return;
        }

        const filteredFacilities = facilities.filter(facility => {
                const sameType = facility.type === selectedType;

                if (!sameType) {
                    return false;
                }

                if (selectedFaculty === '__UNIVERSITAS__') {
                    return (facility.scope_level === 'universitas');
                }

                return (facility.scope_level === 'fakultas' && facility.faculty === selectedFaculty);
            });


        filteredFacilities.forEach(facility => {
            const option = document.createElement('option');
            option.value = facility.id;
            option.textContent = facility.name;

            if (String(facility.id) === String(selectedFacilityId)) {
                option.selected = true;
            }

            facilitySelect.appendChild(option);
        });

        facilitySelect.disabled = false;
    }


    facultySelect.addEventListener('change', function () {
        populateTypes(this.value);
        populateFacilities(this.value, '');
    });

    typeSelect.addEventListener('change', function () {
        populateFacilities(facultySelect.value, this.value);
    });

    facultySelect.value = currentFaculty;

    populateTypes( currentFaculty, currentType);
    populateFacilities(currentFaculty, currentType, currentFacilityId);


    //FOTO UPLOAD
    const photoInput = document.getElementById('photos');
    const photoPreview = document.getElementById('photo-preview');
    const photoError = document.getElementById('photo-error');
    const photoCount = document.getElementById('photo-count');
    const MAX_PHOTOS = 3;
    const MAX_FILE_SIZE = 4 * 1024 * 1024;

    // Jumlah foto yang sudah tersimpan di database
    let existingPhotoCount = {{ $report->photos->count() }};

    // Foto baru yang dipilih user
    let selectedPhotoFiles = [];

    function updatePhotoCount() {
        const deletedCount =  deletedPhotoIds.length;
        const total = existingPhotoCount - deletedCount + selectedPhotoFiles.length;
        photoCount.textContent = total + ' dari ' + MAX_PHOTOS + ' foto digunakan.';
    }

    function showPhotoError(message) {
        photoError.textContent = message;
        photoError.style.display = 'block';
    }

    function clearPhotoError() {
        photoError.textContent = '';
        photoError.style.display = 'none';
    }

    photoInput.addEventListener('change', function () {
            clearPhotoError();

            const newFiles = Array.from(this.files);


            for (const file of newFiles) {
                /*Cek total foto: foto lama + foto baru*/
                const deletedCount = deletedPhotoIds.length;
                const currentPhotoCount = existingPhotoCount - deletedCount + selectedPhotoFiles.length;
                
                if (currentPhotoCount >= MAX_PHOTOS) {
                    showPhotoError('Maksimal 3 foto yang dapat diupload.');
                    break;
                }

                /*Cek apakah file merupakan gambar.*/
                if (!file.type.startsWith('image/')) {
                    showPhotoError('File "' + file.name + '" bukan merupakan gambar.');
                    continue;
                }

                /*Cek ukuran file maksimal 4 MB.*/
                if (file.size > MAX_FILE_SIZE) {
                    showPhotoError('Foto "' + file.name + '" lebih dari 4 MB. ' + 'Silakan pilih foto lain.');
                    continue;
                }

                /*Masukkan foto baru ke daftar.*/
                selectedPhotoFiles.push(file);
            }

            photoInput.value = '';
            syncPhotoInput();
            renderPhotoPreview();
            updatePhotoCount();
        });


    function renderPhotoPreview() {
        photoPreview.innerHTML = '';

        selectedPhotoFiles.forEach(
            function (file, index) {
                const reader = new FileReader();

                reader.onload = function (event) {
                        const photoItem = document.createElement('div');
                        photoItem.className = 'photo-item';
                        
                        photoItem.innerHTML = `
                            <img src="${event.target.result}" alt="Preview foto baru">
                            <button type="button" class="photo-remove" data-index="${index}">×</button>`;

                        photoPreview.appendChild(photoItem);
                    };

                reader.readAsDataURL(file);
            });
    }

    /*Tombol × untuk foto BARU.*/
    photoPreview.addEventListener('click', function (event) {
            if (!event.target.classList.contains('photo-remove')) {
                return;
            }

            const index = Number(event.target.dataset.index);

            selectedPhotoFiles.splice(index, 1);
            syncPhotoInput();
            renderPhotoPreview();
            updatePhotoCount();
        });

    /*Masukkan daftar foto baru ke input.*/
    function syncPhotoInput() {
        const dataTransfer = new DataTransfer();

        selectedPhotoFiles.forEach(function (file) {
                dataTransfer.items.add(file);
        });

        photoInput.files = dataTransfer.files;
    }


    
    /* Konfirmasi penghapusan foto lama.*/
    const existingPhotoPreview = document.getElementById('existing-photo-preview');
    const deletedPhotoInputs = document.getElementById('deleted-photo-inputs');
    const deletedPhotoIds = [];

    const deleteModal = document.getElementById('delete-photo-modal');
    const cancelDeleteButton = document.getElementById('cancel-delete-photo');
    const confirmDeleteButton = document.getElementById('confirm-delete-photo');

    let pendingDeletePhotoId = null;
    let pendingDeletePhotoItem = null;

    function closeDeleteModal() {
        deleteModal.classList.remove('show');
        pendingDeletePhotoId = null;
        pendingDeletePhotoItem = null;
    }

    if (existingPhotoPreview) {
        existingPhotoPreview.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.existing-photo-remove');

            if (!removeButton) {
                return;
            }

            pendingDeletePhotoId = removeButton.dataset.photoId;
            pendingDeletePhotoItem = removeButton.closest('.existing-photo-item');

            deleteModal.classList.add('show');
        });
    }

    // Tombol Batal: tutup tanpa menghapus foto.
    cancelDeleteButton.addEventListener('click', closeDeleteModal);

    // Klik di luar kotak juga membatalkan konfirmasi.
    deleteModal.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });

    // Tombol Ya, Hapus
    confirmDeleteButton.addEventListener('click', function () {
        if (!pendingDeletePhotoId || !pendingDeletePhotoItem) {
            closeDeleteModal();
            return;
        }

        const photoId = pendingDeletePhotoId;

        if (!deletedPhotoIds.includes(photoId)) {
            deletedPhotoIds.push(photoId);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_photo_ids[]';
            input.value = photoId;

            deletedPhotoInputs.appendChild(input);
        }

        pendingDeletePhotoItem.remove();

        updatePhotoCount();
        closeDeleteModal();
    });

    // Tutup modal dengan tombol Escape.
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && deleteModal.classList.contains('show')) {
            closeDeleteModal();
        }
    });

    updatePhotoCount();
});


/* Validasi deskripsi jika kategori "Lainnya" dipilih. */
const editReportForm = document.querySelector('.edit-card form');
const editCategoryInput = document.getElementById('category');
const editDescriptionInput = document.getElementById('description');
const editDescriptionError = document.getElementById('description-error');

editReportForm.addEventListener('submit', function (event) {
    const descriptionRequired =
        editCategoryInput.value === 'Lainnya';

    const isDescriptionEmpty =
        editDescriptionInput.value.trim() === '';

    if (descriptionRequired && isDescriptionEmpty) {
        event.preventDefault();

        editDescriptionError.style.display = 'block';
        editDescriptionInput.focus();
        editDescriptionInput.scrollIntoView({behavior: 'smooth', block: 'center'
        });
        return;
    }

    editDescriptionError.style.display = 'none';
});

</script>

@endsection