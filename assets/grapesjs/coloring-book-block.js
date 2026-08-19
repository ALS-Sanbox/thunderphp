(function () {
    function registerColoringBookBlocks(editor) {
        // window.coloringBooksList is rendered server-side by basic-pages'
        // add.php/edit.php (published coloring_books, {slug, title}) - the
        // same pattern already used for window.imageLibraryUrls. GrapesJS's
        // trait options have to be known at registration time, so this is
        // resolved before the editor even initializes rather than fetched
        // asynchronously here, which would race the trait panel rendering.
        const books = window.coloringBooksList || [];
        const bookOptions = books.map((b) => ({ id: b.slug, label: b.title }));

        editor.DomComponents.addType('coloring-book', {
            isComponent: (el) => !!(el.getAttribute && el.getAttribute('data-thunder-coloring-book') === 'true'),
            model: {
                defaults: {
                    tagName: 'div',
                    draggable: true,
                    droppable: false,
                    attributes: {
                        class: 'thunder-coloring-book',
                        'data-thunder-coloring-book': 'true',
                        'data-coloring-book': bookOptions[0]?.id || '',
                        'data-height': '700px',
                        'data-show-title': 'true',
                        'data-show-page-picker': 'true',
                        'data-show-instructions': 'true',
                        'data-show-fullscreen': 'true',
                        'data-show-print': 'true',
                        'data-show-download': 'true',
                        'data-remember-progress': 'true',
                    },
                    traits: [
                        {
                            type: 'select',
                            name: 'data-coloring-book',
                            label: 'Coloring Book',
                            options: bookOptions.length ? bookOptions : [{ id: '', label: 'No published coloring books yet' }],
                        },
                        { type: 'text', name: 'data-height', label: 'Height' },
                        { type: 'checkbox', name: 'data-show-title', label: 'Show Title', valueTrue: 'true', valueFalse: 'false' },
                        { type: 'checkbox', name: 'data-show-page-picker', label: 'Show Page Picker', valueTrue: 'true', valueFalse: 'false' },
                        { type: 'checkbox', name: 'data-show-instructions', label: 'Show Instructions', valueTrue: 'true', valueFalse: 'false' },
                        { type: 'checkbox', name: 'data-show-fullscreen', label: 'Show Fullscreen', valueTrue: 'true', valueFalse: 'false' },
                        { type: 'checkbox', name: 'data-show-print', label: 'Show Print', valueTrue: 'true', valueFalse: 'false' },
                        { type: 'checkbox', name: 'data-show-download', label: 'Show Download', valueTrue: 'true', valueFalse: 'false' },
                        { type: 'checkbox', name: 'data-remember-progress', label: 'Remember Progress', valueTrue: 'true', valueFalse: 'false' },
                    ],
                },
            },
            // Editor-safe preview: the real widget does its own click/drag/
            // keyboard handling, which is likely to fight GrapesJS's own
            // selection and drag events if it ran live inside the canvas.
            // A static label is shown in the editor instead; the actual
            // interactive widget only ever initializes on the public page,
            // via this plugin's frontend-init.js picking up the rendered
            // .thunder-coloring-book markup.
            view: {
                onRender() {
                    const el = this.el;
                    if (el.querySelector('.tcb-editor-preview')) return;
                    const preview = document.createElement('div');
                    preview.className = 'tcb-editor-preview';
                    preview.style.cssText = 'display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;min-height:200px;padding:32px;border:2px dashed #9ca3af;border-radius:10px;background:#f9fafb;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;color:#374151;text-align:center;';
                    const bookTitle = this.model.getAttributes()['data-coloring-book'] || '(no book selected)';
                    preview.innerHTML =
                        '<div style="font-size:28px;">🎨</div>' +
                        '<strong>' + bookTitle + '</strong>' +
                        '<span style="font-size:0.85rem;color:#6b7280;">Interactive Coloring Book<br>(renders live on the published page)</span>';
                    el.appendChild(preview);
                },
            },
        });

        editor.BlockManager.add('coloring-book', {
            label: 'Coloring Book',
            category: 'Apps',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="40" height="28" rx="2"/><circle cx="15" cy="14" r="3"/><path d="M4 26l10-9 8 7 8-6 14 12"/></svg>',
            content: `<div class="thunder-coloring-book" data-thunder-coloring-book="true" data-coloring-book="${bookOptions[0]?.id || ''}" data-height="700px" data-show-title="true" data-show-page-picker="true" data-show-instructions="true" data-show-fullscreen="true" data-show-print="true" data-show-download="true" data-remember-progress="true"></div>`,
        });
    }

    window.registerColoringBookBlocks = registerColoringBookBlocks;
})();
