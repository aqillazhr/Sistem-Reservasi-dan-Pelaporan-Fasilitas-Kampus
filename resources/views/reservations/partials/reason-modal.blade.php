{{--
    Popup isi alasan, dipakai BERSAMA untuk dua aksi:
    - "Tolak" reservasi pending -> POST ke petugas.reservations.reject, field "note"
    - "Batalkan" reservasi approved -> POST ke petugas.reservations.petugas-cancel, field "cancellation_reason"

    Halaman pemanggil cukup kasih tombol dengan atribut:
      data-reason-url="{{ route(...) }}"
      data-reason-field="note" ATAU "cancellation_reason"
      data-reason-title="Tolak reservasi ini?"
    lalu JS di sini yang urus sisanya (buka modal, submit, dsb).
--}}
<dialog id="reasonModal">
    <div class="reason-modal-body">
        <h2 id="reasonTitle">Isi alasan</h2>
        <form method="POST" id="reasonForm">
            @csrf
            <label for="reasonInput">Alasan</label>
            <textarea id="reasonInput" rows="4" maxlength="500" required placeholder="Tulis alasannya di sini..."></textarea>
            <div class="err" id="reasonErr" role="alert" hidden></div>
            <div class="reason-modal-actions">
                <button type="button" class="btn ghost" id="reasonCancelBtn">Batal</button>
                <button type="submit" class="btn primary" id="reasonSubmitBtn">Kirim</button>
            </div>
        </form>
    </div>
</dialog>

<style>
    dialog#reasonModal {
        border: none;
        border-radius: 14px;
        padding: 0;
        width: min(480px, 92vw);
        box-shadow: 0 20px 60px rgba(39, 15, 69, .35);
    }
    dialog#reasonModal::backdrop {
        background: rgba(39, 15, 69, .55);
        backdrop-filter: blur(2px);
    }
    .reason-modal-body { padding: 24px 28px; font-family: 'Sora', Helvetica, sans-serif; }
    .reason-modal-body h2 { margin: 0 0 16px; font-size: 20px; color: #270F45; }
    .reason-modal-body label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; color: #270F45; }
    .reason-modal-body textarea {
        width: 100%; padding: 10px 12px; border: 1px solid #BD93F8; border-radius: 8px;
        font: inherit; resize: vertical;
    }
    .reason-modal-body .err { color: #b42318; font-size: 13px; margin-top: 6px; }
    .reason-modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; }
    .reason-modal-actions .btn { padding: 9px 18px; border-radius: 8px; border: 0; font: inherit; font-weight: 600; cursor: pointer; }
    .reason-modal-actions .btn.primary { background: #9747FF; color: #fff; }
    .reason-modal-actions .btn.ghost { background: #FBF7FF; color: #270F45; border: 1px solid #BD93F8; }
</style>

<script>
(function () {
    var modal = document.getElementById('reasonModal');
    var form = document.getElementById('reasonForm');
    var title = document.getElementById('reasonTitle');
    var input = document.getElementById('reasonInput');
    var err = document.getElementById('reasonErr');
    var submitBtn = document.getElementById('reasonSubmitBtn');

    // Dipanggil dari halaman manapun yang sudah menyertakan partial ini.
    window.openReasonModal = function (url, fieldName, titleText) {
        form.action = url;
        input.name = fieldName;
        input.value = '';
        title.textContent = titleText || 'Isi alasan';
        err.hidden = true;
        submitBtn.disabled = false;
        modal.showModal();
    };

    // Event delegation: tombol "Tolak"/"Batalkan" ada di banyak baris tabel,
    // semuanya cukup diberi atribut data-reason-* (lihat komentar di atas).
    document.addEventListener('click', function (ev) {
        var btn = ev.target.closest('[data-reason-url]');
        if (! btn) return;
        ev.preventDefault();
        openReasonModal(btn.dataset.reasonUrl, btn.dataset.reasonField, btn.dataset.reasonTitle);
    });

    document.getElementById('reasonCancelBtn').addEventListener('click', function () { modal.close(); });

    form.addEventListener('submit', function (ev) {
        if (input.value.trim().length < 3) {
            ev.preventDefault();
            err.textContent = 'Alasan wajib diisi (minimal 3 karakter).';
            err.hidden = false;
            return;
        }
        submitBtn.disabled = true; // cegah klik ganda
    });
})();
</script>
