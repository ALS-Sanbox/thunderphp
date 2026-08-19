/*!
 * Vendored, unmodified copy of SVGColoringWidget from ALS-Sanbox/Coloring-Book-App
 * @ commit 53abbd6d686f69bf9a03fbe2e76d34664cbd4d17
 *
 * This file is synced FROM that repository, not developed here - it's the
 * generic, ThunderPHP-agnostic coloring engine. To update it: pull the
 * latest svg-coloring-widget.js from Coloring-Book-App and replace this
 * file (and its accompanying CSS) wholesale, updating the commit hash
 * above. Don't hand-edit this copy directly; changes belong upstream.
 */
window.SVGColoringWidget = (function() {

  // ---------------------------------------------------------------------
  // Inline icons - no Font Awesome or any other icon-font dependency.
  // Simple, single-color (currentColor) line/shape icons, 24x24 viewbox.
  // ---------------------------------------------------------------------
  const ICONS = {
    zoomIn: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="6.5"/><line x1="15.3" y1="15.3" x2="21" y2="21"/><line x1="7.5" y1="10.5" x2="13.5" y2="10.5"/><line x1="10.5" y1="7.5" x2="10.5" y2="13.5"/></svg>',
    zoomOut: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="6.5"/><line x1="15.3" y1="15.3" x2="21" y2="21"/><line x1="7.5" y1="10.5" x2="13.5" y2="10.5"/></svg>',
    resetView: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/></svg>',
    fullscreen: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9V5a1 1 0 0 1 1-1h4"/><path d="M20 9V5a1 1 0 0 0-1-1h-4"/><path d="M4 15v4a1 1 0 0 0 1 1h4"/><path d="M20 15v4a1 1 0 0 1-1 1h-4"/></svg>',
    panUp: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M6 11l6-6 6 6"/></svg>',
    panDown: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M18 13l-6 6-6-6"/></svg>',
    panLeft: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M11 6l-6 6 6 6"/></svg>',
    panRight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>',
    center: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="2.5"/><path d="M12 3v3.5M12 17.5V21M3 12h3.5M17.5 12H21"/></svg>',
    undo: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 8L3 12l4 4"/><path d="M3 12h11a6 6 0 0 1 0 12h-1"/></svg>',
    redo: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8l4 4-4 4"/><path d="M21 12H10a6 6 0 0 0 0 12h1"/></svg>',
    resetColoring: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>',
    print: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="4" y="9" width="16" height="8" rx="1"/><path d="M6 17v4h12v-4"/></svg>',
    download: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>',
  };

  function icon(name) {
    return ICONS[name] || '';
  }

  // ---------------------------------------------------------------------
  // Sanitization - DOMParser-based, not regex. Applies to every public
  // entry point that loads SVG content from an untrusted source (a URL, a
  // string, a File). ThunderPHP (or any other host page) also sanitizes
  // SVG server-side before it's ever stored - this is a second,
  // independent layer, not a replacement for that.
  // ---------------------------------------------------------------------
  const DANGEROUS_TAGS = ['script', 'foreignObject', 'iframe', 'object', 'embed', 'link', 'meta'];
  const URL_ATTRS = ['href', 'xlink:href', 'src'];
  const DANGEROUS_URL_PATTERN = /^\s*(javascript|vbscript|data:text\/html)/i;

  function isDangerousUrl(value) {
    if (!value) return false;
    return DANGEROUS_URL_PATTERN.test(value);
  }

  function sanitizeSvgElement(root) {
    // Elements are removed outright - script, foreignObject, and embed-like
    // tags have no legitimate role in a coloring page.
    DANGEROUS_TAGS.forEach((tag) => {
      root.querySelectorAll(tag).forEach((el) => el.remove());
    });

    const walker = document.createTreeWalker(root, NodeFilter.SHOW_ELEMENT, null);
    const toStrip = [];
    let node = walker.currentNode;
    while (node) {
      const attrs = Array.from(node.attributes || []);
      attrs.forEach((attr) => {
        const name = attr.name.toLowerCase();

        // Every event-handler attribute (onclick, onload, onerror, ...).
        if (name.startsWith('on')) {
          toStrip.push([node, attr.name]);
          return;
        }

        // href/xlink:href/src pointing at javascript:, vbscript:, or an
        // HTML data: URL. Internal #fragment references (gradients,
        // patterns, clip-paths defined in the same document) are left
        // alone - coloring SVGs depend on those.
        if (URL_ATTRS.includes(attr.name.toLowerCase()) && isDangerousUrl(attr.value)) {
          toStrip.push([node, attr.name]);
          return;
        }

        // A style attribute can carry a javascript: URL inside url(...) or
        // (legacy IE) expression(...).
        if (name === 'style' && /(javascript:|expression\s*\()/i.test(attr.value)) {
          toStrip.push([node, attr.name]);
        }
      });
      node = walker.nextNode();
    }
    toStrip.forEach(([el, attrName]) => el.removeAttribute(attrName));

    return root;
  }

  /**
   * Parses and sanitizes an SVG string. Returns the sanitized outerHTML
   * string, or null if the input isn't a well-formed SVG document at all.
   */
  function sanitizeSVG(svgText) {
    if (typeof svgText !== 'string' || !svgText.trim()) return null;

    const parser = new DOMParser();
    const doc = parser.parseFromString(svgText, 'image/svg+xml');

    if (doc.querySelector('parsererror')) return null;

    const root = doc.documentElement;
    if (!root || root.tagName.toLowerCase() !== 'svg') return null;

    sanitizeSvgElement(root);

    return new XMLSerializer().serializeToString(root);
  }

  // ---------------------------------------------------------------------
  // Local progress persistence - IndexedDB, falling back to localStorage
  // if IndexedDB isn't available (private-browsing modes in some older
  // browsers, etc). No accounts, no server round-trip: this only ever
  // touches the visitor's own browser.
  // ---------------------------------------------------------------------
  const DB_NAME = 'svg-coloring-widget';
  const DB_STORE = 'progress';
  let dbPromise = null;

  function openDb() {
    if (!window.indexedDB) return Promise.reject(new Error('indexedDB unavailable'));
    if (dbPromise) return dbPromise;

    dbPromise = new Promise((resolve, reject) => {
      const req = indexedDB.open(DB_NAME, 1);
      req.onupgradeneeded = () => {
        if (!req.result.objectStoreNames.contains(DB_STORE)) {
          req.result.createObjectStore(DB_STORE);
        }
      };
      req.onsuccess = () => resolve(req.result);
      req.onerror = () => reject(req.error);
    });

    return dbPromise;
  }

  async function idbSet(key, value) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
      const tx = db.transaction(DB_STORE, 'readwrite');
      tx.objectStore(DB_STORE).put(value, key);
      tx.oncomplete = () => resolve();
      tx.onerror = () => reject(tx.error);
    });
  }

  async function idbGet(key) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
      const tx = db.transaction(DB_STORE, 'readonly');
      const req = tx.objectStore(DB_STORE).get(key);
      req.onsuccess = () => resolve(req.result);
      req.onerror = () => reject(req.error);
    });
  }

  async function idbDelete(key) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
      const tx = db.transaction(DB_STORE, 'readwrite');
      tx.objectStore(DB_STORE).delete(key);
      tx.oncomplete = () => resolve();
      tx.onerror = () => reject(tx.error);
    });
  }

  const LOCALSTORAGE_PREFIX = 'svg-coloring-widget:';

  async function persistSave(key, svgString) {
    try {
      await idbSet(key, svgString);
      return;
    } catch (e) {
      // IndexedDB unavailable or failed - fall back to localStorage.
    }
    try {
      localStorage.setItem(LOCALSTORAGE_PREFIX + key, svgString);
    } catch (e) {
      // Storage full or disabled entirely - progress just won't persist.
    }
  }

  async function persistLoad(key) {
    try {
      const value = await idbGet(key);
      if (value) return value;
    } catch (e) {
      // fall through to localStorage
    }
    try {
      return localStorage.getItem(LOCALSTORAGE_PREFIX + key);
    } catch (e) {
      return null;
    }
  }

  async function persistClear(key) {
    try {
      await idbDelete(key);
    } catch (e) {
      // ignore
    }
    try {
      localStorage.removeItem(LOCALSTORAGE_PREFIX + key);
    } catch (e) {
      // ignore
    }
  }

  const MAX_HISTORY = 40;

  class ColoringWidget {
    constructor(container, options = {}) {
      this.container = typeof container === 'string' ? document.querySelector(container) : container;
      this.options = {
        width: '100%',
        height: '100%',
        sidebarWidth: '300px',
        display: 'flex',
        persistenceKey: null,
        autoSave: false,
        ...options
      };

      this.currentMode = 'solid';
      this.svgDoc = null;
      this.zoomLevel = 1;
      this.panX = 0;
      this.panY = 0;
      this.gradientType = 'linear';
      this.gradientAngle = 0;
      this.patternCache = {};
      this.selectedPattern = null;

      // Undo/redo: bounded array of full-SVG-markup snapshots. Only
      // coloring actions (applyFill) push a snapshot - pan/zoom/mode
      // switches never touch history, so history stays meaningful and
      // bounded regardless of how much the view itself is nudged around.
      this.history = [];
      this.historyIndex = -1;
      this.originalSvgMarkup = null;

      this._autoSaveTimer = null;

      this.palettes = {
        base: ['#f44336','#e91e63','#9c27b0','#673ab7','#3f51b5','#2196f3','#03a9f4','#00bcd4','#009688','#4caf50','#8bc34a','#cddc39','#ffeb3b','#ffc107','#ff9800','#ff5722'],
        pastels: ['#FFB3BA','#FFDFBA','#FFFFBA','#BAFFC9','#BAE1FF','#D7BAFF','#FFC3E6','#E0F7FA','#FFD6A5','#C1E1C1','#F6E2B3','#D3C4E3','#F3E5AB','#B0E0E6','#F5C6AA','#E6E6FA'],
        brights: ['#e6194b','#3cb44b','#ffe119','#4363d8','#f58231','#911eb4','#46f0f0','#f032e6','#bcf60c','#fabebe','#008080','#e6beff','#9a6324','#fffac8','#800000','#aaffc3']
      };

      this.demoPatterns = {
        'dots': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <rect width="20" height="20" fill="currentColor1"/>
          <circle cx="10" cy="10" r="3" fill="currentColor2"/>
        </svg>`,
        'stripes': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
          <rect width="10" height="10" fill="currentColor1"/>
          <rect x="0" y="0" width="5" height="10" fill="currentColor2"/>
        </svg>`,
        'checkers': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <rect width="20" height="20" fill="currentColor1"/>
          <rect x="0" y="0" width="10" height="10" fill="currentColor2"/>
          <rect x="10" y="10" width="10" height="10" fill="currentColor2"/>
        </svg>`,
        'zigzag': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <rect width="20" height="20" fill="currentColor1"/>
          <path d="M0,10 L5,5 L10,10 L15,5 L20,10 L20,20 L0,20 Z" fill="currentColor2"/>
        </svg>`,
        'triangles': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <rect width="20" height="20" fill="currentColor1"/>
          <polygon points="10,2 18,18 2,18" fill="currentColor2"/>
        </svg>`,
        'pattern00': `<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="701.5" height="707" viewBox="608.5,110.5,701.5,707"><g id="document" fill="currentColor1" fill-rule="nonzero" stroke="#000000" stroke-width="0" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><rect x="610" y="79.64286" transform="scale(1,1.4)" width="700" height="500" id="Shape 1 1" vector-effect="non-scaling-stroke"/></g><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g id="stage"><g id="layer1 1"><path d="M610,112l697,705l-347,-3l6,-703z" id="Path 1" fill="currentColor2" stroke="currentColor2"/><path d="M965.99154,111.99139" id="Path 1" fill="none" stroke="#000000"/><path d="M966,112l343,344v-345z" id="Path 1" fill="currentColor2" stroke="#000000"/><path d="M960.0256,811.00022l-350.0256,-425.00022l-1,424z" id="Path 1" fill="currentColor2" stroke="currentColor2"/></g></g></g></svg>`,
        'pattern01': `<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="701" height="700" viewBox="610,105.5,701,700">
          <g id="document" fill="currentColor1" fill-rule="nonzero" ><rect x="610" y="75.35714" transform="scale(1,1.4)" width="700" height="500" id="Shape 1 1" vector-effect="non-scaling-stroke" fill="currentColor1" />
          </g>
          <!-- Start look for fill="#8c8c8c" and change to fill="currentColor2"-->
          <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="none" stroke-linejoin="none" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
          <g id="stage">
          <g id="layer1 1">
          <path d="M931,241" id="Path 1" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M543.35363,750.45148" id="Path 1" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1237.70605,244.71895" id="Path 1" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M619.18521,809.69861" id="Path 1" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M618.79559,713.5363" id="Path 1" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1230.80407,115.63317" id="Path 1" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1231.59607,110.93577" id="Path 1" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1310.83255,196.31714l-1.18686,-84.33446" id="Path 1" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <g id="Group 1" fill="currentColor2">
          <path d="M968.77196,266.07648l-54.99726,120.19577l-129.00274,1.80423l86.77778,91.22222l-31.22222,149.77778l124.9645,-85.95077l129.47994,92.95077l-35,-144l99,-102l-123.47859,4.15113z" id="Path 4" stroke="#8c8c8c" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M682.56067,111.35243l-72.54649,0.03604l-0.73516,84.85559l39.46018,-26.11836l115.0355,82.95077l-33.95709,-140.92861l-47.25694,-0.79543" id="Path 2 1" stroke="#000000" stroke-linecap="butt" stroke-linejoin="miter"/>
          <path d="M1309.89609,111.98604l-78.30002,-1.05027c-12.59092,77.57035 -17.49544,106.65924 -26.15744,155.14071l105.50432,-69.83241" id="Path 3 1" stroke="#000000" stroke-linecap="butt" stroke-linejoin="miter"/>
          <path d="M1310.94295,704.54246l-129.00274,1.80423l79,89l-2.84256,16.85929l52.51552,-0.35988z" id="Path 4" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M608.79811,706.55443l1.59748,104.98187l144.57421,0.66968l98.80217,-101.79617l-123.47859,4.15113l-66.52141,-128.15113l-54.99726,120.19577" id="Path 1 1" stroke="#000000" stroke-linecap="butt" stroke-linejoin="miter"/>
          </g>
          </g>
          </g>
          </g>
          <!-- End -->
          </svg>`,
        'pattern03': `<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="701" height="700" viewBox="610,105.5,701,700">
          <g id="document" fill="currentColor1" fill-rule="nonzero" ><rect x="610" y="75.35714" transform="scale(1,1.4)" width="700" height="500" id="Shape 1 1" vector-effect="non-scaling-stroke" fill="currentColor1" />
          </g>
          <!-- Start look for fill="#8c8c8c" and change to fill="currentColor2"-->
          <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
          <g id="stage">
          <g id="layer1 1"><path d="M610,811.9854l342,-409.9854l360.07137,411.93761z" id="Path 1" fill="currentColor2" stroke="#8c8c8c"/>
          <path d="M958.95479,409.91806" id="Path 1" fill="none" stroke="#000000"/>
          <path d="M611,195l341.93133,208.06032l-342.93133,-0.06032z" id="Path 1" fill="currentColor2" stroke="#000000"/>
          <path d="M1311,208" id="Path 1" fill="none" stroke="#000000"/><path d="M1311.95003,195l-359.0187,208.06032l360.06867,-0.06032z" id="Path 1" fill="currentColor2" stroke="#000000"/>
          </g>
          </g>
          </g>
          <!-- End -->
          </svg>`,
      };

      this._initPromise = this.init();
    }

    async init() {
      this.createHTML();
      this.bindEvents();
      this.initializePalette();
      this.initializePatterns();
      this.updateGradientButtons();
      this.updateHistoryButtons();

      if (this.options.src) {
        await this.loadSVGFromURL(this.options.src);
      } else if (this.options.persistenceKey && this.options.autoSave) {
        await this._tryRestoreProgress();
      }
    }

    createHTML() {
      this.container.innerHTML = `
        <div class="svg-coloring-widget" style="width: ${this.options.width}; height: ${this.options.height}; display: ${this.options.display};">
          <div class="sidebar" style="width: ${this.options.sidebarWidth};">
            <h3 style="margin-top: 0; color: #333;">Coloring Controls</h3>

            <div class="control-group">
              <label id="zoom-controls-label">Zoom Controls</label>
              <div class="zoom-controls" role="group" aria-labelledby="zoom-controls-label">
                <button type="button" class="zoom-in" title="Zoom In" aria-label="Zoom in">${icon('zoomIn')}</button>
                <button type="button" class="zoom-out" title="Zoom Out" aria-label="Zoom out">${icon('zoomOut')}</button>
                <button type="button" class="reset-zoom" title="Reset Zoom" aria-label="Reset zoom and pan">${icon('resetView')}</button>
                <button type="button" class="fullscreen-toggle" title="Full Screen" aria-label="Toggle full screen">${icon('fullscreen')}</button>
              </div>
              <label id="pan-controls-label">Pan Controls</label>
              <div class="pan-controls" role="group" aria-labelledby="pan-controls-label">
                <button type="button" class="pan-up" title="Pan Up" aria-label="Pan up">${icon('panUp')}</button>
                <button type="button" class="pan-left" title="Pan Left" aria-label="Pan left">${icon('panLeft')}</button>
                <button type="button" class="pan-right" title="Pan Right" aria-label="Pan right">${icon('panRight')}</button>
                <button type="button" class="pan-down" title="Pan Down" aria-label="Pan down">${icon('panDown')}</button>
                <button type="button" class="pan-center" title="Center Image" aria-label="Center image">${icon('center')}</button>
              </div>
            </div>

            <div class="control-group">
              <label id="action-controls-label">Actions</label>
              <div class="action-controls" role="group" aria-labelledby="action-controls-label">
                <button type="button" class="undo-btn" title="Undo" aria-label="Undo last coloring change">${icon('undo')}</button>
                <button type="button" class="redo-btn" title="Redo" aria-label="Redo coloring change">${icon('redo')}</button>
                <button type="button" class="reset-coloring-btn" title="Reset Coloring" aria-label="Reset coloring to blank">${icon('resetColoring')}</button>
                <button type="button" class="print-btn" title="Print" aria-label="Print picture">${icon('print')}</button>
                <button type="button" class="download-svg-btn" title="Download SVG" aria-label="Download as SVG">${icon('download')}</button>
              </div>
            </div>

            <div class="control-group">
              <label id="fill-mode-label">Fill Mode</label>
              <div class="mode-buttons" role="group" aria-labelledby="fill-mode-label">
                <button type="button" class="mode-button selected" data-mode="solid" title="Solid Fill" aria-label="Solid fill" aria-pressed="true">
                  <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" fill="#333"/></svg>
                </button>
                <button type="button" class="mode-button" data-mode="gradient" title="Gradient Fill" aria-label="Gradient fill" aria-pressed="false">
                  <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                    <defs><linearGradient id="iconGrad"><stop offset="0%" stop-color="#f00"/><stop offset="100%" stop-color="#00f"/></linearGradient></defs>
                    <rect x="4" y="4" width="16" height="16" fill="url(#iconGrad)"/>
                  </svg>
                </button>
                <button type="button" class="mode-button" data-mode="pattern" title="Pattern Fill" aria-label="Pattern fill" aria-pressed="false">
                  <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                    <defs><pattern id="iconPat" width="4" height="4" patternUnits="userSpaceOnUse"><rect width="4" height="4" fill="#ccc"/><circle cx="2" cy="2" r="1" fill="#333"/></pattern></defs>
                    <rect x="4" y="4" width="16" height="16" fill="url(#iconPat)"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="solid-controls control-group">
              <div class="palette-group control-group">
                <label id="palette-label">Select Palette</label>
                <div class="palette-buttons" role="group" aria-labelledby="palette-label">
                  <button type="button" class="palette-button selected" data-palette="base" aria-pressed="true">Base</button>
                  <button type="button" class="palette-button" data-palette="pastels" aria-pressed="false">Pastels</button>
                  <button type="button" class="palette-button" data-palette="brights" aria-pressed="false">Brights</button>
                </div>
                <div class="swatch-container" role="group" aria-label="Color swatches"></div>
              </div>
              <label for="scw-color-solid">Solid Color</label>
              <input type="color" id="scw-color-solid" class="color-solid" value="#ff0000">
            </div>

            <div class="gradient-controls control-group" style="display:none;">
              <label>Gradient Colors</label>
              <input type="color" class="color-grad1" value="#ffffff" aria-label="Gradient start color" style="margin-right: 10px;">
              <input type="color" class="color-grad2" value="#e91e63" aria-label="Gradient end color">
              <div class="gradient-buttons" role="group" aria-label="Gradient direction">
                <button type="button" class="gradient-button" data-type="linear" data-angle="0" aria-label="Linear gradient, left to right"></button>
                <button type="button" class="gradient-button" data-type="linear" data-angle="180" aria-label="Linear gradient, right to left"></button>
                <button type="button" class="gradient-button" data-type="linear" data-angle="90" aria-label="Linear gradient, bottom to top"></button>
                <button type="button" class="gradient-button" data-type="linear" data-angle="-90" aria-label="Linear gradient, top to bottom"></button>
                <button type="button" class="gradient-button" data-type="linear" data-angle="45" aria-label="Linear gradient, diagonal"></button>
                <button type="button" class="gradient-button" data-type="linear" data-angle="-45" aria-label="Linear gradient, diagonal"></button>
                <button type="button" class="gradient-button" data-type="linear" data-angle="30" aria-label="Linear gradient, diagonal"></button>
                <button type="button" class="gradient-button" data-type="linear" data-angle="-30" aria-label="Linear gradient, diagonal"></button>
                <button type="button" class="gradient-button" data-type="radial" aria-label="Radial gradient"></button>
              </div>
            </div>

            <div class="pattern-controls control-group" style="display:none;">
              <label>Pattern Colors</label>
              <input type="color" class="pattern-color1" value="#eeeeee" title="Background Color" aria-label="Pattern background color" style="margin-right: 10px;">
              <input type="color" class="pattern-color2" value="#333333" title="Foreground Color" aria-label="Pattern foreground color">
              <label class="range-label" for="scw-pattern-scale">Pattern Scale: <span class="scale-value">40</span>px</label>
              <input type="range" id="scw-pattern-scale" class="pattern-scale" min="5" max="200" value="40">
              <label id="pattern-type-label">Pattern Type</label>
              <div class="pattern-buttons" role="group" aria-labelledby="pattern-type-label"></div>
            </div>

            <div class="info-text">
              Click any region in the picture to apply the selected fill. Use Undo/Redo to fix mistakes, or Reset Coloring to start the picture over.
            </div>
          </div>

          <div class="viewer">
            <div class="svg-container"></div>
          </div>
        </div>
      `;
    }

    bindEvents() {
      const widget = this.container.querySelector('.svg-coloring-widget');

      // Mode buttons
      widget.querySelectorAll('.mode-button').forEach((btn) => {
        btn.addEventListener('click', () => this.setMode(btn.dataset.mode));
      });

      // Zoom controls
      widget.querySelector('.zoom-in').addEventListener('click', () => this.zoomIn());
      widget.querySelector('.zoom-out').addEventListener('click', () => this.zoomOut());
      widget.querySelector('.reset-zoom').addEventListener('click', () => this.resetZoom());
      widget.querySelector('.fullscreen-toggle').addEventListener('click', () => this.fullscreen());

      // Pan controls
      widget.querySelector('.pan-up').addEventListener('click', () => this.panUp());
      widget.querySelector('.pan-down').addEventListener('click', () => this.panDown());
      widget.querySelector('.pan-left').addEventListener('click', () => this.panLeft());
      widget.querySelector('.pan-right').addEventListener('click', () => this.panRight());
      widget.querySelector('.pan-center').addEventListener('click', () => this.centerPan());

      // History/action controls
      widget.querySelector('.undo-btn').addEventListener('click', () => this.undo());
      widget.querySelector('.redo-btn').addEventListener('click', () => this.redo());
      widget.querySelector('.reset-coloring-btn').addEventListener('click', () => {
        if (window.confirm('Reset this picture back to blank? This clears all coloring on it.')) {
          this.resetColoring();
        }
      });
      widget.querySelector('.print-btn').addEventListener('click', () => this.print());
      widget.querySelector('.download-svg-btn').addEventListener('click', () => this.downloadSVG());

      // Gradient controls
      widget.querySelector('.color-grad1').addEventListener('input', () => this.updateGradientButtons());
      widget.querySelector('.color-grad2').addEventListener('input', () => this.updateGradientButtons());

      // Pattern controls
      widget.querySelector('.pattern-color1').addEventListener('input', () => this.updatePatternPreviews());
      widget.querySelector('.pattern-color2').addEventListener('input', () => this.updatePatternPreviews());
      const patternScale = widget.querySelector('.pattern-scale');
      patternScale.addEventListener('input', () => {
        widget.querySelector('.scale-value').textContent = patternScale.value;
        this.updatePatternPreviews();
      });

      // Palette buttons
      widget.querySelectorAll('.palette-button').forEach((btn) => {
        btn.addEventListener('click', () => this.setPalette(btn.dataset.palette, btn));
      });

      // Keyboard shortcuts for undo/redo, scoped to this widget instance.
      widget.addEventListener('keydown', (e) => {
        const isUndo = (e.ctrlKey || e.metaKey) && !e.shiftKey && e.key.toLowerCase() === 'z';
        const isRedo = (e.ctrlKey || e.metaKey) && (e.shiftKey && e.key.toLowerCase() === 'z' || e.key.toLowerCase() === 'y');
        if (isUndo) {
          e.preventDefault();
          this.undo();
        } else if (isRedo) {
          e.preventDefault();
          this.redo();
        }
      });
    }

    // Kept for a consumer that wires up its own <input type="file">
    // element - not used by createHTML()'s own template, but if a host
    // page adds one, this makes sure it goes through the same sanitizer
    // as every other loading path instead of assigning raw file content.
    handleFileLoad(event) {
      const file = event.target.files[0];
      if (file && file.type === 'image/svg+xml') {
        const reader = new FileReader();
        reader.onload = (e) => this.loadSVG(e.target.result);
        reader.readAsText(file);
      }
    }

    setMode(mode) {
      this.currentMode = mode;
      const widget = this.container.querySelector('.svg-coloring-widget');

      widget.querySelectorAll('.mode-button').forEach((btn) => {
        const isSelected = btn.dataset.mode === mode;
        btn.classList.toggle('selected', isSelected);
        btn.setAttribute('aria-pressed', String(isSelected));
      });

      widget.querySelector('.solid-controls').style.display = mode === 'solid' ? 'block' : 'none';
      widget.querySelector('.gradient-controls').style.display = mode === 'gradient' ? 'block' : 'none';
      widget.querySelector('.pattern-controls').style.display = mode === 'pattern' ? 'block' : 'none';
    }

    onSvgClick(event) {
      if (!this.svgDoc) return;
      const target = event.target;
      if (!['path', 'rect', 'circle', 'polygon', 'ellipse'].includes(target.tagName)) return;
      this.applyFill(target);
      this.pushHistory();
      this._scheduleAutoSave();
    }

    ensureDefs() {
      if (!this.svgDoc) return;
      let defs = this.svgDoc.querySelector('defs');
      if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        this.svgDoc.insertBefore(defs, this.svgDoc.firstChild);
      }
    }

    applyFill(element) {
      this.ensureDefs();
      const widget = this.container.querySelector('.svg-coloring-widget');

      if (this.currentMode === 'solid') {
        const color = widget.querySelector('.color-solid').value;
        element.setAttribute('fill', color);
      }
      else if (this.currentMode === 'gradient') {
        const id = `grad-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
        const grad = document.createElementNS('http://www.w3.org/2000/svg',
          this.gradientType === 'radial' ? 'radialGradient' : 'linearGradient');
        grad.setAttribute('id', id);

        if (this.gradientType === 'linear') {
          const angle = parseFloat(this.gradientAngle || 0);
          const x1 = 50 - 50 * Math.cos(angle * Math.PI / 180);
          const y1 = 50 - 50 * Math.sin(angle * Math.PI / 180);
          const x2 = 50 + 50 * Math.cos(angle * Math.PI / 180);
          const y2 = 50 + 50 * Math.sin(angle * Math.PI / 180);
          grad.setAttribute('x1', `${x1}%`);
          grad.setAttribute('y1', `${y1}%`);
          grad.setAttribute('x2', `${x2}%`);
          grad.setAttribute('y2', `${y2}%`);
        }

        const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop1.setAttribute('offset', '0%');
        stop1.setAttribute('stop-color', widget.querySelector('.color-grad1').value);
        const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop2.setAttribute('offset', '100%');
        stop2.setAttribute('stop-color', widget.querySelector('.color-grad2').value);
        grad.appendChild(stop1);
        grad.appendChild(stop2);
        this.svgDoc.querySelector('defs').appendChild(grad);
        element.setAttribute('fill', `url(#${id})`);
      }
      else if (this.currentMode === 'pattern' && this.selectedPattern) {
        const id = `pat-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
        const cached = this.patternCache[this.selectedPattern];
        if (!cached) return;

        const patternEl = cached.cloneNode(true);
        const bgColor = widget.querySelector('.pattern-color1').value;
        const fgColor = widget.querySelector('.pattern-color2').value;
        const scale = widget.querySelector('.pattern-scale').value;

        this.applyPatternColors(patternEl, bgColor, fgColor, scale);
        patternEl.setAttribute('id', id);

        this.svgDoc.querySelector('defs').appendChild(patternEl);
        element.setAttribute('fill', `url(#${id})`);
      }
    }

    applyPatternColors(patternEl, bgColor, fgColor, scale) {
      patternEl.setAttribute('patternUnits', 'userSpaceOnUse');
      patternEl.setAttribute('width', scale);
      patternEl.setAttribute('height', scale);

      const processElement = (el, inheritedFill = null, inheritedStroke = null) => {
        let currentFill = el.getAttribute('fill') || inheritedFill;
        let currentStroke = el.getAttribute('stroke') || inheritedStroke;

        if (currentFill === 'currentColor1') {
          currentFill = bgColor;
          el.setAttribute('fill', bgColor);
        } else if (currentFill === 'currentColor2') {
          currentFill = fgColor;
          el.setAttribute('fill', fgColor);
        }

        if (currentStroke === 'currentColor1') {
          currentStroke = bgColor;
          el.setAttribute('stroke', bgColor);
        } else if (currentStroke === 'currentColor2') {
          currentStroke = fgColor;
          el.setAttribute('stroke', fgColor);
        }

        if (el.tagName === 'g') {
          const groupFill = el.getAttribute('fill');
          if (groupFill && groupFill.includes('currentColor')) {
            currentFill = groupFill === 'currentColor1' ? bgColor : fgColor;
            el.removeAttribute('fill');
          }
        }

        Array.from(el.children).forEach((child) => {
          processElement(child, currentFill, currentStroke);
        });
      };

      Array.from(patternEl.children).forEach((child) => {
        processElement(child);
      });
    }

    initializePalette() {
      this.setPalette('base', this.container.querySelector('[data-palette="base"]'));
    }

    setPalette(paletteName, button) {
      const widget = this.container.querySelector('.svg-coloring-widget');

      widget.querySelectorAll('.palette-button').forEach((btn) => {
        const isSelected = btn === button;
        btn.classList.toggle('selected', isSelected);
        btn.setAttribute('aria-pressed', String(isSelected));
      });

      const palette = this.palettes[paletteName];
      const swatchContainer = widget.querySelector('.swatch-container');
      swatchContainer.innerHTML = '';

      palette.forEach((color) => {
        const swatch = document.createElement('button');
        swatch.type = 'button';
        swatch.className = 'swatch';
        swatch.style.backgroundColor = color;
        swatch.setAttribute('aria-label', `Color ${color}`);
        swatch.addEventListener('click', () => {
          widget.querySelector('.color-solid').value = color;
          widget.querySelectorAll('.swatch').forEach((s) => s.classList.remove('selected'));
          swatch.classList.add('selected');
        });
        swatchContainer.appendChild(swatch);
      });
    }

    updateGradientButtons() {
      const widget = this.container.querySelector('.svg-coloring-widget');
      const color1 = widget.querySelector('.color-grad1').value;
      const color2 = widget.querySelector('.color-grad2').value;

      widget.querySelectorAll('.gradient-button').forEach((btn) => {
        if (!btn.dataset.bound) {
          btn.dataset.bound = '1';
          btn.addEventListener('click', () => {
            widget.querySelectorAll('.gradient-button').forEach((b) => b.classList.remove('selected'));
            btn.classList.add('selected');
            this.gradientType = btn.dataset.type;
            this.gradientAngle = btn.dataset.angle || 0;
          });
        }

        if (btn.dataset.type === 'linear') {
          const angle = btn.dataset.angle || '0';
          btn.style.background = `linear-gradient(${angle}deg, ${color1}, ${color2})`;
        } else {
          btn.style.background = `radial-gradient(circle, ${color1}, ${color2})`;
        }
      });
    }

    initializePatterns() {
      const widget = this.container.querySelector('.svg-coloring-widget');
      const patternContainer = widget.querySelector('.pattern-buttons');

      Object.keys(this.demoPatterns).forEach((patternName) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'pattern-button';
        btn.dataset.pattern = patternName;
        btn.setAttribute('aria-label', `${patternName} pattern`);

        const svgPreview = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svgPreview.setAttribute('width', '100%');
        svgPreview.setAttribute('height', '100%');
        svgPreview.setAttribute('viewBox', '0 0 100 100');
        svgPreview.setAttribute('aria-hidden', 'true');
        btn.appendChild(svgPreview);
        patternContainer.appendChild(btn);

        // demoPatterns are authored by this library itself, not
        // user/network input, but parsing them the same way any other SVG
        // string is parsed keeps this one code path for turning SVG text
        // into a pattern DOM node.
        const parser = new DOMParser();
        const svgDoc = parser.parseFromString(this.demoPatterns[patternName], 'image/svg+xml');
        const svgEl = svgDoc.querySelector('svg');
        const viewBox = svgEl.getAttribute('viewBox') || '0 0 100 100';

        const patternEl = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
        patternEl.setAttribute('id', `preview-${patternName}`);
        patternEl.setAttribute('patternUnits', 'userSpaceOnUse');
        patternEl.setAttribute('viewBox', viewBox);
        patternEl.setAttribute('width', 100);
        patternEl.setAttribute('height', 100);

        Array.from(svgEl.children).forEach((child) => {
          patternEl.appendChild(child.cloneNode(true));
        });

        this.patternCache[patternName] = patternEl;

        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        const previewPattern = patternEl.cloneNode(true);
        previewPattern.setAttribute('id', `preview-${patternName}`);

        this.applyPatternColors(previewPattern,
          widget.querySelector('.pattern-color1').value,
          widget.querySelector('.pattern-color2').value, 100);

        defs.appendChild(previewPattern);
        svgPreview.appendChild(defs);

        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        rect.setAttribute('width', '100%');
        rect.setAttribute('height', '100%');
        rect.setAttribute('fill', `url(#preview-${patternName})`);
        svgPreview.appendChild(rect);

        btn.addEventListener('click', () => {
          widget.querySelectorAll('.pattern-button').forEach((b) => b.classList.remove('selected'));
          btn.classList.add('selected');
          this.selectedPattern = patternName;
        });
      });
    }

    updatePatternPreviews() {
      const widget = this.container.querySelector('.svg-coloring-widget');
      const bgColor = widget.querySelector('.pattern-color1').value;
      const fgColor = widget.querySelector('.pattern-color2').value;
      const scale = widget.querySelector('.pattern-scale').value;

      widget.querySelectorAll('.pattern-button').forEach((btn) => {
        const patternName = btn.dataset.pattern;
        const svg = btn.querySelector('svg');
        if (!svg) return;

        const original = this.patternCache[patternName];
        if (!original) return;

        const patternClone = original.cloneNode(true);
        patternClone.setAttribute('id', `preview-${patternName}`);
        this.applyPatternColors(patternClone, bgColor, fgColor, scale);

        const defs = svg.querySelector('defs');
        defs.innerHTML = '';
        defs.appendChild(patternClone);
      });
    }

    zoomIn() {
      this.zoomLevel *= 1.2;
      this.updateTransform();
    }

    zoomOut() {
      this.zoomLevel /= 1.2;
      this.updateTransform();
    }

    resetZoom() {
      this.zoomLevel = 1;
      this.panX = 0;
      this.panY = 0;
      this.updateTransform();
    }

    fullscreen() {
      const widget = this.container.querySelector('.svg-coloring-widget');

      if (!document.fullscreenElement) {
        if (widget.requestFullscreen) {
          widget.requestFullscreen().catch((err) => {
            console.error(`Error attempting to enable fullscreen: ${err.message}`);
          });
        } else if (widget.webkitRequestFullscreen) {
          widget.webkitRequestFullscreen();
        } else if (widget.msRequestFullscreen) {
          widget.msRequestFullscreen();
        } else if (widget.mozRequestFullScreen) {
          widget.mozRequestFullScreen();
        }
      } else if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      }
    }

    panUp() {
      this.panY += 50;
      this.updateTransform();
    }

    panDown() {
      this.panY -= 50;
      this.updateTransform();
    }

    panLeft() {
      this.panX += 50;
      this.updateTransform();
    }

    panRight() {
      this.panX -= 50;
      this.updateTransform();
    }

    centerPan() {
      this.panX = 0;
      this.panY = 0;
      this.updateTransform();
    }

    updateTransform() {
      const container = this.container.querySelector('.svg-container');
      container.style.transform = `translate(${this.panX}px, ${this.panY}px) scale(${this.zoomLevel})`;
    }

    // -----------------------------------------------------------------
    // Loading
    // -----------------------------------------------------------------

    /**
     * Loads SVG markup directly. Always sanitizes first - this is the one
     * place that actually swaps in new SVG content, so every other
     * loading path (loadSVGFromURL, handleFileLoad, init({src})) routes
     * through this rather than touching svg-container.innerHTML itself.
     */
    loadSVG(svgContent) {
      const sanitized = sanitizeSVG(svgContent);
      if (sanitized === null) {
        this._showError('That picture could not be loaded - the file was not a valid SVG.');
        return false;
      }

      const svgContainer = this.container.querySelector('.svg-container');
      svgContainer.innerHTML = sanitized;
      this.svgDoc = svgContainer.querySelector('svg');

      if (this.svgDoc) {
        this.svgDoc.addEventListener('click', (e) => this.onSvgClick(e));
        this.svgDoc.style.maxWidth = '100%';
        this.svgDoc.style.maxHeight = '100%';
        this.ensureDefs();
      }

      this.originalSvgMarkup = sanitized;
      this.history = [sanitized];
      this.historyIndex = 0;
      this.updateHistoryButtons();
      this._clearOverlay();

      return true;
    }

    /**
     * Fetches an SVG from a URL, sanitizes it, and loads it. Never leaves
     * the widget in a broken state on failure - the previously-loaded
     * picture (if any) stays exactly as it was, and an error is surfaced
     * both as a thrown/rejected error and a brief on-widget message.
     */
    async loadSVGFromURL(url) {
      this._showLoading();
      try {
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`Failed to load SVG (HTTP ${response.status})`);
        }
        const text = await response.text();
        const loaded = this.loadSVG(text);
        if (!loaded) {
          throw new Error('The fetched file was not a valid SVG.');
        }
        return true;
      } catch (err) {
        this._showError('Could not load this picture. Please try again.');
        throw err;
      } finally {
        this._clearLoading();
      }
    }

    _showLoading() {
      this._clearOverlay();
      const viewer = this.container.querySelector('.viewer');
      const overlay = document.createElement('div');
      overlay.className = 'loading-overlay';
      overlay.textContent = 'Loading picture...';
      viewer.appendChild(overlay);
    }

    _clearLoading() {
      const overlay = this.container.querySelector('.loading-overlay');
      if (overlay) overlay.remove();
    }

    _showError(message) {
      this._clearOverlay();
      const viewer = this.container.querySelector('.viewer');
      const overlay = document.createElement('div');
      overlay.className = 'error-overlay';
      overlay.setAttribute('role', 'alert');
      overlay.textContent = message;
      viewer.appendChild(overlay);
      setTimeout(() => overlay.remove(), 4000);
    }

    _clearOverlay() {
      this.container.querySelectorAll('.loading-overlay, .error-overlay').forEach((el) => el.remove());
    }

    getSVG() {
      return this.svgDoc ? this.svgDoc.outerHTML : null;
    }

    setFillMode(mode) {
      if (['solid', 'gradient', 'pattern'].includes(mode)) {
        this.setMode(mode);
      }
    }

    // -----------------------------------------------------------------
    // Undo / redo / reset
    // -----------------------------------------------------------------

    // history[0] is always the original, as-loaded picture; history[N] is
    // the state after the Nth coloring action. historyIndex points at
    // whichever of those is currently on screen, so undo/redo are just
    // "move the pointer and restore that snapshot" - not separate
    // before/after bookkeeping.
    pushHistory() {
      if (!this.svgDoc) return;
      // Discard any redo branch once a new coloring action happens.
      this.history = this.history.slice(0, this.historyIndex + 1);
      this.history.push(this.svgDoc.outerHTML);
      if (this.history.length > MAX_HISTORY) {
        this.history.shift();
      }
      this.historyIndex = this.history.length - 1;
      this.updateHistoryButtons();
    }

    undo() {
      if (this.historyIndex <= 0) return;
      this.historyIndex -= 1;
      this._restoreSnapshot(this.history[this.historyIndex]);
      this._scheduleAutoSave();
    }

    redo() {
      if (this.historyIndex >= this.history.length - 1) return;
      this.historyIndex += 1;
      this._restoreSnapshot(this.history[this.historyIndex]);
      this._scheduleAutoSave();
    }

    _restoreSnapshot(svgOuterHtml) {
      // Snapshots are captured from the live, already-sanitized DOM, not
      // from an external source - re-running the full sanitizer here
      // would be redundant, but the load path is still shared so the
      // click listener and defs get reattached consistently.
      const svgContainer = this.container.querySelector('.svg-container');
      svgContainer.innerHTML = svgOuterHtml;
      this.svgDoc = svgContainer.querySelector('svg');
      if (this.svgDoc) {
        this.svgDoc.addEventListener('click', (e) => this.onSvgClick(e));
        this.svgDoc.style.maxWidth = '100%';
        this.svgDoc.style.maxHeight = '100%';
        this.ensureDefs();
      }
      this.updateHistoryButtons();
    }

    updateHistoryButtons() {
      const widget = this.container.querySelector('.svg-coloring-widget');
      if (!widget) return;
      const undoBtn = widget.querySelector('.undo-btn');
      const redoBtn = widget.querySelector('.redo-btn');
      if (undoBtn) undoBtn.disabled = this.historyIndex <= 0;
      if (redoBtn) redoBtn.disabled = this.historyIndex >= this.history.length - 1;
    }

    /**
     * Restores the picture to how it looked when it was first loaded,
     * clearing all coloring. No confirmation dialog here by design - the
     * toolbar button asks first; resetColoring() itself just does it, so
     * a host page driving the widget programmatically isn't forced
     * through a browser confirm() it may not want.
     */
    resetColoring() {
      if (!this.originalSvgMarkup) return;
      const svgContainer = this.container.querySelector('.svg-container');
      svgContainer.innerHTML = this.originalSvgMarkup;
      this.svgDoc = svgContainer.querySelector('svg');
      if (this.svgDoc) {
        this.svgDoc.addEventListener('click', (e) => this.onSvgClick(e));
        this.svgDoc.style.maxWidth = '100%';
        this.svgDoc.style.maxHeight = '100%';
        this.ensureDefs();
      }
      this.history = [this.originalSvgMarkup];
      this.historyIndex = 0;
      this.updateHistoryButtons();
      this._scheduleAutoSave();
    }

    // -----------------------------------------------------------------
    // Export: download and print
    // -----------------------------------------------------------------

    downloadSVG(filename) {
      const svg = this.getSVG();
      if (!svg) return;
      const blob = new Blob([svg], { type: 'image/svg+xml' });
      this._downloadBlob(blob, filename || 'coloring-page.svg');
    }

    /**
     * Rasterizes the current picture to a PNG. Renders at a resolution
     * multiplier above the SVG's own natural size (rather than whatever
     * size it happens to display on screen) so the download is print/
     * share-quality instead of a tiny screen-size bitmap.
     */
    downloadPNG(filename, options = {}) {
      const svg = this.getSVG();
      if (!svg) return Promise.reject(new Error('No picture loaded.'));

      const scale = options.scale || 3;
      const viewBox = this.svgDoc.getAttribute('viewBox');
      let width = parseFloat(this.svgDoc.getAttribute('width')) || 0;
      let height = parseFloat(this.svgDoc.getAttribute('height')) || 0;

      if ((!width || !height) && viewBox) {
        const parts = viewBox.split(/\s+/).map(Number);
        if (parts.length === 4) {
          width = parts[2];
          height = parts[3];
        }
      }
      width = width || 1000;
      height = height || 1000;

      return new Promise((resolve, reject) => {
        const svgBlob = new Blob([svg], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);
        const img = new Image();

        img.onload = () => {
          const canvas = document.createElement('canvas');
          canvas.width = Math.round(width * scale);
          canvas.height = Math.round(height * scale);
          const ctx = canvas.getContext('2d');
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(0, 0, canvas.width, canvas.height);
          ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
          URL.revokeObjectURL(url);

          canvas.toBlob((blob) => {
            if (!blob) {
              reject(new Error('PNG export failed.'));
              return;
            }
            this._downloadBlob(blob, filename || 'coloring-page.png');
            resolve(true);
          }, 'image/png');
        };

        img.onerror = () => {
          URL.revokeObjectURL(url);
          reject(new Error('PNG export failed - the picture could not be rasterized.'));
        };

        img.src = url;
      });
    }

    _downloadBlob(blob, filename) {
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      link.remove();
      setTimeout(() => URL.revokeObjectURL(url), 1000);
    }

    /**
     * Prints just the picture - not the surrounding page, toolbar, or
     * sidebar. Uses a hidden same-origin iframe (rather than window.open,
     * which popup blockers commonly stop) that's created, printed, and
     * torn down for each print() call.
     */
    print() {
      const svg = this.getSVG();
      if (!svg) return;

      const iframe = document.createElement('iframe');
      iframe.style.position = 'fixed';
      iframe.style.right = '0';
      iframe.style.bottom = '0';
      iframe.style.width = '0';
      iframe.style.height = '0';
      iframe.style.border = '0';
      document.body.appendChild(iframe);

      const doc = iframe.contentWindow.document;
      doc.open();
      doc.write(`<!DOCTYPE html><html><head><title>Coloring Page</title><style>
        html, body { margin: 0; padding: 0; }
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        svg { max-width: 100%; max-height: 100vh; }
      </style></head><body>${svg}</body></html>`);
      doc.close();

      const cleanup = () => {
        setTimeout(() => iframe.remove(), 500);
      };

      iframe.contentWindow.addEventListener('afterprint', cleanup);
      // Fallback in case afterprint doesn't fire (some browsers/print flows).
      setTimeout(cleanup, 5000);

      iframe.onload = () => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
      };
    }

    // -----------------------------------------------------------------
    // Local progress persistence
    // -----------------------------------------------------------------

    async saveProgress(key) {
      const svg = this.getSVG();
      if (!svg) return false;
      await persistSave(key, svg);
      return true;
    }

    async loadProgress(key) {
      const saved = await persistLoad(key);
      if (!saved) return false;
      // Progress was already sanitized when it was first saved (it's a
      // snapshot of svgDoc.outerHTML from the live, already-sanitized
      // widget) - it's still routed back through loadSVG() rather than
      // assigned directly, since it's coming out of browser storage the
      // widget doesn't fully control (another script on the same origin
      // could have written to the same key).
      return this.loadSVG(saved);
    }

    async clearProgress(key) {
      await persistClear(key);
    }

    _scheduleAutoSave() {
      if (!this.options.autoSave || !this.options.persistenceKey) return;
      clearTimeout(this._autoSaveTimer);
      this._autoSaveTimer = setTimeout(() => {
        this.saveProgress(this.options.persistenceKey);
      }, 600);
    }

    async _tryRestoreProgress() {
      try {
        await this.loadProgress(this.options.persistenceKey);
      } catch (e) {
        // No saved progress, or it failed to load - nothing to do, the
        // widget just stays on whatever's already loaded (or empty).
      }
    }

    destroy() {
      clearTimeout(this._autoSaveTimer);
      this.container.innerHTML = '';
    }
  }

  return {
    init: function(container, options) {
      return new ColoringWidget(container, options);
    },

    sanitizeSVG,

    // Auto-initialize every element with data-svg-coloring.
    autoInit: function() {
      document.querySelectorAll('[data-svg-coloring]').forEach((el) => {
        const options = {};
        if (el.dataset.width) options.width = el.dataset.width;
        if (el.dataset.height) options.height = el.dataset.height;
        if (el.dataset.sidebarWidth) options.sidebarWidth = el.dataset.sidebarWidth;
        if (el.dataset.svgSrc) options.src = el.dataset.svgSrc;
        if (el.dataset.persistenceKey) options.persistenceKey = el.dataset.persistenceKey;
        if (el.dataset.autoSave === 'true') options.autoSave = true;

        new ColoringWidget(el, options);
      });
    }
  };
})();

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', SVGColoringWidget.autoInit);
} else {
  SVGColoringWidget.autoInit();
}
