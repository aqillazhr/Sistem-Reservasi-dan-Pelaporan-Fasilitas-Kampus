{{--
    Popup konfirmasi generik untuk menggantikan window.confirm() bawaan browser.
    Dipakai untuk aksi "Setuju", dll.
    
    Cara pakai:
    Tambahkan class "form-confirm" pada <form> dan beri atribut data-confirm-title dan data-confirm-msg.
    Contoh:
    <form method="POST" class="form-confirm" data-confirm-title="Setujui Reservasi" data-confirm-msg="Anda yakin ingin menyetujui reservasi ini?">
--}}
<dialog id="confirmModal">
    <div class="confirm-modal-body">
        <h2 id="confirmTitle">Konfirmasi</h2>
        <p id="confirmMsg">Apakah Anda yakin?</p>
        <div class="confirm-modal-actions">
            <button type="button" class="btn ghost" id="confirmCancelBtn">Batal</button>
            <button type="button" class="btn primary" id="confirmSubmitBtn">Ya, Lanjutkan</button>
        </div>
    </div>
</dialog>

<style>
    dialog#confirmModal {
        border: none;
        border-radius: 14px;
        padding: 0;
        width: min(400px, 92vw);
        box-shadow: 0 20px 60px rgba(39, 15, 69, .35);
    }
    dialog#confirmModal::backdrop {
        background: rgba(39, 15, 69, .55);
        backdrop-filter: blur(2px);
    }
    .confirm-modal-body { padding: 24px 28px; font-family: 'Sora', Helvetica, sans-serif; text-align: center; }
    .confirm-modal-body h2 { margin: 0 0 12px; font-size: 20px; color: #270F45; }
    .confirm-modal-body p { margin: 0 0 24px; font-size: 15px; color: #525151; line-height: 1.5; }
    .confirm-modal-actions { display: flex; justify-content: center; gap: 10px; }
    .confirm-modal-actions .btn { padding: 9px 18px; border-radius: 8px; border: 0; font: inherit; font-weight: 600; cursor: pointer; }
    .confirm-modal-actions .btn.primary { background: #9747FF; color: #fff; }
    .confirm-modal-actions .btn.ghost { background: #FBF7FF; color: #270F45; border: 1px solid #BD93F8; }
</style>

<script>
(function () {
    var modal = document.getElementById('confirmModal');
    if (!modal) return;
    
    var titleEl = document.getElementById('confirmTitle');
    var msgEl = document.getElementById('confirmMsg');
    var cancelBtn = document.getElementById('confirmCancelBtn');
    var submitBtn = document.getElementById('confirmSubmitBtn');
    var currentForm = null;

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.classList.contains('form-confirm')) {
            e.preventDefault();
            currentForm = form;
            titleEl.textContent = form.getAttribute('data-confirm-title') || 'Konfirmasi';
            msgEl.textContent = form.getAttribute('data-confirm-msg') || 'Apakah Anda yakin ingin melanjutkan?';
            modal.showModal();
        }
    });

    cancelBtn.addEventListener('click', function () {
        modal.close();
        currentForm = null;
    });

    submitBtn.addEventListener('click', function () {
        if (currentForm) {
            // Remove the class so it doesn't trigger the interceptor again
            currentForm.classList.remove('form-confirm');
            currentForm.submit();
        }
    });
})();
</script>
