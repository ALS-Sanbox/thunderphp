document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.copy-url-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url = btn.getAttribute('data-url');

            navigator.clipboard.writeText(url).then(function () {
                const icon = btn.querySelector('i');
                icon.classList.remove('bi-clipboard');
                icon.classList.add('bi-clipboard-check');

                setTimeout(function () {
                    icon.classList.remove('bi-clipboard-check');
                    icon.classList.add('bi-clipboard');
                }, 1500);
            });
        });
    });

    const previewModal = document.getElementById('imagePreviewModal');
    if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            previewModal.querySelector('#imagePreviewImg').src = trigger.getAttribute('data-full-src');
            previewModal.querySelector('#imagePreviewLabel').textContent = trigger.getAttribute('data-name') || 'Preview';
        });
    }
});
