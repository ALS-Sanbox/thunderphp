(function ($) {
    function getCsrfToken() {
        var tokenInput = document.querySelector('input[name="_token"]');
        return tokenInput ? tokenInput.value : '';
    }

    function buildModal() {
        if (document.getElementById('snImageLibraryModal')) {
            return;
        }

        var modalHtml = ''
            + '<div class="modal fade" id="snImageLibraryModal" tabindex="-1" aria-hidden="true">'
            + '  <div class="modal-dialog modal-lg modal-dialog-centered">'
            + '    <div class="modal-content">'
            + '      <div class="modal-header py-2">'
            + '        <h6 class="modal-title mb-0">Insert Image</h6>'
            + '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>'
            + '      </div>'
            + '      <div class="modal-body">'
            + '        <label class="form-label fw-bold">Media Library</label>'
            + '        <div id="snImageLibraryGrid" class="sn-image-library-grid mb-3"><p class="text-muted small mb-0">Loading...</p></div>'
            + '        <hr>'
            + '        <div class="mb-3">'
            + '          <label class="form-label fw-bold">Select from Files</label>'
            + '          <input type="file" id="snImageLibraryFile" accept="image/*" class="form-control">'
            + '          <div id="snImageLibraryUploadStatus" class="small text-muted mt-1"></div>'
            + '        </div>'
            + '        <div class="mb-2">'
            + '          <label class="form-label fw-bold">Image URL</label>'
            + '          <div class="input-group">'
            + '            <input type="text" id="snImageLibraryUrl" class="form-control" placeholder="https://example.com/image.jpg">'
            + '            <button type="button" id="snImageLibraryUrlBtn" class="btn btn-outline-primary">Insert</button>'
            + '          </div>'
            + '        </div>'
            + '      </div>'
            + '    </div>'
            + '  </div>'
            + '</div>';

        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function loadImages(listUrl) {
        var grid = document.getElementById('snImageLibraryGrid');
        grid.innerHTML = '<p class="text-muted small mb-0">Loading...</p>';

        fetch(listUrl, { credentials: 'same-origin' })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!Array.isArray(data) || data.length === 0) {
                    grid.innerHTML = '<p class="text-muted small mb-0">No images uploaded yet.</p>';
                    return;
                }

                grid.innerHTML = '';
                data.forEach(function (img) {
                    var thumb = document.createElement('img');
                    thumb.src = img.url;
                    thumb.title = img.name;
                    thumb.className = 'sn-image-library-thumb';
                    thumb.addEventListener('click', function () {
                        window.snImageLibraryInsert(img.url);
                    });
                    grid.appendChild(thumb);
                });
            })
            .catch(function () {
                grid.innerHTML = '<p class="text-danger small mb-0">Failed to load images.</p>';
            });
    }

    $.extend(true, $.summernote.plugins, {
        imageLibrary: function (context) {
            var ui = $.summernote.ui;
            var options = context.options;
            var listUrl = options.imageLibrary && options.imageLibrary.listUrl;
            var uploadUrl = options.imageLibrary && options.imageLibrary.uploadUrl;

            context.memo('button.imageLibrary', function () {
                var button = ui.button({
                    contents: '<i class="bi bi-image"></i>',
                    tooltip: 'Insert Image',
                    click: function () {
                        buildModal();

                        window.snImageLibraryInsert = function (url) {
                            context.invoke('editor.insertImage', url);
                            var modalEl = document.getElementById('snImageLibraryModal');
                            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modal.hide();
                        };

                        loadImages(listUrl);

                        var modalEl = document.getElementById('snImageLibraryModal');
                        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.show();

                        var status = document.getElementById('snImageLibraryUploadStatus');
                        var fileInput = document.getElementById('snImageLibraryFile');
                        fileInput.value = '';
                        status.textContent = '';

                        fileInput.onchange = function () {
                            if (!fileInput.files.length) return;

                            status.textContent = 'Uploading...';

                            var formData = new FormData();
                            formData.append('images[]', fileInput.files[0]);
                            formData.append('_token', getCsrfToken());
                            formData.append('ajax', '1');

                            fetch(uploadUrl, {
                                method: 'POST',
                                body: formData,
                                credentials: 'same-origin',
                            })
                                .then(function (res) { return res.json(); })
                                .then(function (data) {
                                    if (data.success && data.images && data.images.length) {
                                        status.textContent = '';
                                        window.snImageLibraryInsert(data.images[0].url);
                                    } else {
                                        status.textContent = 'Upload failed: ' + (data.errors ? data.errors.join(' ') : 'Unknown error');
                                    }
                                })
                                .catch(function () {
                                    status.textContent = 'Upload failed.';
                                });
                        };

                        var urlBtn = document.getElementById('snImageLibraryUrlBtn');
                        urlBtn.onclick = function () {
                            var urlInput = document.getElementById('snImageLibraryUrl');
                            if (urlInput.value.trim()) {
                                window.snImageLibraryInsert(urlInput.value.trim());
                                urlInput.value = '';
                            }
                        };
                    }
                });
                return button.render();
            });
        }
    });
})(jQuery);
