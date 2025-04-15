    function previewFile(inputId, previewId) {
        const fileInput = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const file = fileInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onloadend = function () {
                preview.src = reader.result;
                preview.classList.remove('loaded'); // reset in case
                preview.onload = () => preview.classList.add('loaded');
            }
            reader.readAsDataURL(file);
        }
    }

    function clearImage(inputId, previewId, fallbackSrc) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        input.value = '';
        preview.src = fallbackSrc;
        preview.classList.remove('loaded');
    }

    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function () {
        const slugField = document.getElementById('slug');
        const slug = this.value
            .toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
        if (!slugField.dataset.modified) {
            slugField.value = slug;
        }
    });

    // Mark slug as manually modified
    document.getElementById('slug').addEventListener('input', function () {
        this.dataset.modified = true;
    });
