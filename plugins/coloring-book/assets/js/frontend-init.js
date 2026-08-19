/*
 * Always loaded on every public page (it's tiny) - only pulls in the much
 * heavier SVGColoringWidget engine, and only makes an API call, if it
 * actually finds a .thunder-coloring-book block on the page. A page
 * without the block costs nothing beyond this file itself.
 */
(function () {
    function loadAsset(tag, attrs) {
        return new Promise(function (resolve, reject) {
            const el = document.createElement(tag);
            Object.keys(attrs).forEach((key) => el.setAttribute(key, attrs[key]));
            el.onload = () => resolve();
            el.onerror = () => reject(new Error('Failed to load ' + (attrs.href || attrs.src)));
            document.head.appendChild(el);
        });
    }

    function loadWidgetAssets(basePath) {
        return Promise.all([
            loadAsset('link', { rel: 'stylesheet', href: basePath + 'assets/css/svg-coloring-widget.css' }),
            loadAsset('link', { rel: 'stylesheet', href: basePath + 'assets/css/frontend.css' }),
        ]).then(() => loadAsset('script', { src: basePath + 'assets/js/svg-coloring-widget.js' }));
    }

    function truthy(value) {
        return value !== undefined && value !== 'false' && value !== '0';
    }

    function renderPagePicker(container, book, onSelect) {
        const picker = document.createElement('div');
        picker.className = 'tcb-page-picker';

        const heading = document.createElement('h3');
        heading.className = 'tcb-page-picker-heading';
        heading.textContent = 'Choose a Picture';
        picker.appendChild(heading);

        const grid = document.createElement('div');
        grid.className = 'tcb-page-picker-grid';

        book.pages.forEach((page, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tcb-page-picker-item';
            btn.setAttribute('aria-label', page.title);

            const img = document.createElement('img');
            img.src = page.thumbnail || page.svg || '';
            img.alt = '';
            img.loading = 'lazy';

            const label = document.createElement('span');
            label.textContent = page.title;

            btn.appendChild(img);
            btn.appendChild(label);
            btn.addEventListener('click', () => onSelect(page, index));

            grid.appendChild(btn);
        });

        picker.appendChild(grid);
        container.appendChild(picker);
    }

    function initBlock(el) {
        const slug = el.dataset.coloringBook;
        if (!slug) return;

        const height = el.dataset.height || '700px';
        const showTitle = truthy(el.dataset.showTitle);
        const showPagePicker = el.dataset.showPagePicker === undefined ? true : truthy(el.dataset.showPagePicker);
        const showInstructions = truthy(el.dataset.showInstructions);
        const showFullscreen = el.dataset.showFullscreen === undefined ? true : truthy(el.dataset.showFullscreen);
        const showPrint = el.dataset.showPrint === undefined ? true : truthy(el.dataset.showPrint);
        const showDownload = el.dataset.showDownload === undefined ? true : truthy(el.dataset.showDownload);
        const rememberProgress = el.dataset.rememberProgress === undefined ? true : truthy(el.dataset.rememberProgress);

        const apiBase = window.coloringBookApiBase || '/coloring-book/api/';

        fetch(apiBase + encodeURIComponent(slug))
            .then((response) => {
                if (!response.ok) throw new Error('Coloring book not found (HTTP ' + response.status + ')');
                return response.json();
            })
            .then((book) => {
                el.innerHTML = '';
                el.classList.add('tcb-block');

                if (showTitle) {
                    const title = document.createElement('h2');
                    title.className = 'tcb-title';
                    title.textContent = book.title;
                    el.appendChild(title);
                }

                if (showInstructions) {
                    const instructions = document.createElement('p');
                    instructions.className = 'tcb-instructions';
                    instructions.textContent = book.description || 'Pick a picture below, then click any area to color it in.';
                    el.appendChild(instructions);
                }

                if (!book.pages || !book.pages.length) {
                    const empty = document.createElement('p');
                    empty.className = 'tcb-empty';
                    empty.textContent = 'No pages have been published in this coloring book yet.';
                    el.appendChild(empty);
                    return;
                }

                const widgetHost = document.createElement('div');
                widgetHost.className = 'tcb-widget-host';
                widgetHost.style.height = height;

                const persistenceKeyFor = (pageSlug) => 'coloring:' + book.slug + ':' + pageSlug;

                let widget = null;
                let currentPage = book.pages[0];

                function applyToolbarVisibility() {
                    if (!widget) return;
                    const root = widgetHost.querySelector('.svg-coloring-widget');
                    if (!root) return;
                    if (!showFullscreen) root.querySelector('.fullscreen-toggle')?.style.setProperty('display', 'none');
                    if (!showPrint) root.querySelector('.print-btn')?.style.setProperty('display', 'none');
                    if (!showDownload) root.querySelector('.download-svg-btn')?.style.setProperty('display', 'none');
                }

                function loadPage(page) {
                    currentPage = page;
                    const key = persistenceKeyFor(page.slug);

                    if (!widget) {
                        widget = SVGColoringWidget.init(widgetHost, {
                            height: '100%',
                            src: page.svg,
                            persistenceKey: rememberProgress ? key : null,
                            autoSave: rememberProgress,
                        });
                        widget._initPromise.then(applyToolbarVisibility);
                        return;
                    }

                    const proceed = () => {
                        if (rememberProgress) {
                            widget.loadProgress(key).then((loaded) => {
                                if (!loaded) widget.loadSVGFromURL(page.svg).catch(() => {});
                            });
                        } else {
                            widget.loadSVGFromURL(page.svg).catch(() => {});
                        }
                    };

                    proceed();
                }

                if (showPagePicker) {
                    renderPagePicker(el, book, (page) => loadPage(page));
                }

                el.appendChild(widgetHost);
                loadPage(currentPage);
            })
            .catch((err) => {
                el.innerHTML = '<p class="tcb-error">This coloring book could not be loaded.</p>';
                console.error('[coloring-book]', err);
            });
    }

    function init() {
        const blocks = document.querySelectorAll('.thunder-coloring-book');
        if (!blocks.length) return;

        // Resolve the plugin's own base URL from this very script tag,
        // rather than hardcoding /plugins/coloring-book/ - matches how the
        // rest of the CMS avoids hardcoded plugin paths.
        const thisScript = document.currentScript || Array.from(document.scripts).find((s) => s.src.includes('coloring-book'));
        const basePath = thisScript ? thisScript.src.replace(/assets\/js\/frontend-init\.js.*$/, '') : '/plugins/coloring-book/';

        loadWidgetAssets(basePath).then(() => {
            blocks.forEach(initBlock);
        }).catch((err) => console.error('[coloring-book] failed to load widget assets', err));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
