  document.getElementById('description').addEventListener('input', function () {
    const maxLength = 155;
    const currentLength = this.value.length;
    const counter = document.getElementById('descCounter');

    counter.textContent = `${currentLength} / ${maxLength} characters`;

    if (currentLength > maxLength) {
      counter.classList.remove('text-muted');
      counter.classList.add('text-danger');
    } else {
      counter.classList.remove('text-danger');
      counter.classList.add('text-muted');
    }
  });

  document.getElementById('showEditorBtn').addEventListener('click', function () {
    document.getElementById('basic-container').style.display = 'none';
    document.getElementById('gjs-container').style.display = 'block';
    document.getElementById('showDetailsBtn').style.display = 'inline-block';
    this.style.display = 'none';

    document.getElementById('advanced').checked = true;

    const grapesCss = document.createElement('link');
    grapesCss.rel = 'stylesheet';
    grapesCss.href = grapesBasePath + 'grapes.min.css';
    document.head.appendChild(grapesCss);

    const grapesScript = document.createElement('script');
    grapesScript.src = grapesBasePath + 'grapes.min.js';
    grapesScript.onload = function () {
      const pluginScripts = [
        'grapesjs-blocks-basic.min.js',
        'grapesjs-plugin-forms.min.js',
        'grapesjs-navbar.min.js',
        'grapesjs-custom-code.min.js',
        'grapesjs-preset-webpage.min.js',
      ];

      let loadedPlugins = 0;

      pluginScripts.forEach((plugin) => {
        const pluginScript = document.createElement('script');
        pluginScript.src = grapesBasePath + plugin;
        pluginScript.onload = function () {
          loadedPlugins++;
          if (loadedPlugins === pluginScripts.length) {
            initGrapesEditor();
          }
        };
        document.body.appendChild(pluginScript);
      });
    };

    document.body.appendChild(grapesScript);
  });

  document.getElementById('showDetailsBtn').addEventListener('click', function () {
    document.getElementById('basic-container').style.display = 'flex';
    document.getElementById('gjs-container').style.display = 'none';
    document.getElementById('showEditorBtn').style.display = 'inline-block';
    this.style.display = 'none';

    document.getElementById('advanced').checked = false;
  });

  let grapesEditor;

	// Reads the same _token the page's own form already renders via csrf()
	// (id="pageForm") rather than duplicating the CMS's CSRF issuing logic.
	function getPageCsrfToken() {
	  const field = document.querySelector('#pageForm input[name="_token"]');
	  return field ? field.value : '';
	}

	function initGrapesEditor() {
	  grapesEditor = grapesjs.init({
		container: '#gjs',
		fromElement: true,
		height: '100vh',
		width: 'auto',
		showOffsets: true,
        allowScripts: true,
        storageManager: false,
		plugins: [
		  'gjs-blocks-basic',
		  'grapesjs-plugin-forms',
		  'grapesjs-custom-code',
		  'grapesjs-navbar',
 		  'grapesjs-preset-webpage',
		],
		pluginsOpts: {
		  'grapesjs-custom-code': {
			modalTitle: 'Edit Custom Code',
		  },
		},
		assets: {
		  storageType: 'self',
		  uploadName: 'images',
		  onUpload: async ({ files }) => {
			const body = new FormData();
			for (const file of files) {
			  body.append('images[]', file);
			}
			body.append('ajax', '1');
			body.append('_token', getPageCsrfToken());

			const response = await fetch(window.imageLibraryUrls.uploadUrl, {
			  method: 'POST',
			  body,
			});

			if (!response.ok) {
			  throw new Error('Image upload failed (HTTP ' + response.status + ')');
			}

			const result = await response.json();
			if (!result.success) {
			  throw new Error((result.errors && result.errors.join(' ')) || 'Image upload failed');
			}

			// The Images plugin's own upload endpoint - the same one Summernote's
			// image library already uses via imageLibraryUrls - returns
			// {images: [{id, url, name}]}, not the {src} shape GrapesJS's asset
			// manager expects back from onUpload.
			return result.images.map((image) => ({ src: image.url, name: image.name }));
		  },
		}
	  });

	  if (window.registerNavBlocks) {
		window.registerNavBlocks(grapesEditor);
	  }

	  if (window.registerHeroBlocks) {
		window.registerHeroBlocks(grapesEditor);
	  }

	  if (window.registerFooterBlocks) {
		window.registerFooterBlocks(grapesEditor);
	  }

	  if (window.registerFeatureBlocks) {
		window.registerFeatureBlocks(grapesEditor);
	  }

	  if (window.registerCtaBlocks) {
		window.registerCtaBlocks(grapesEditor);
	  }

	  if (window.registerTestimonialBlocks) {
		window.registerTestimonialBlocks(grapesEditor);
	  }

	  if (window.registerPricingBlocks) {
		window.registerPricingBlocks(grapesEditor);
	  }

	  if (window.registerTeamBlocks) {
		window.registerTeamBlocks(grapesEditor);
	  }

	  if (window.registerFaqBlocks) {
		window.registerFaqBlocks(grapesEditor);
	  }

	  if (window.registerContactBlocks) {
		window.registerContactBlocks(grapesEditor);
	  }

	  if (window.registerColoringBookBlocks) {
		window.registerColoringBookBlocks(grapesEditor);
	  }
	}


  document.addEventListener('DOMContentLoaded', function () {

    $('.summernote').summernote({
      placeholder: 'Hello put content here',
      tabsize: 2,
      height: 600,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'imageLibrary', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']],
      ],
      imageLibrary: window.imageLibraryUrls || {},
    });

    function slugify(text) {
      return text.toString().toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, ''); 
    }

    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');

    titleInput.addEventListener('input', function () {
      if (!slugInput.dataset.manualEdit || slugInput.dataset.manualEdit === "false") {
        slugInput.value = slugify(this.value);
      }
    });

    slugInput.addEventListener('input', function () {
      this.dataset.manualEdit = "true";
    });

    const form = document.getElementById('pageForm');
    form.addEventListener('submit', function () {
      if (grapesEditor) {
        let gjsContent = grapesEditor.getHtml() + '<style>' + grapesEditor.getCss() + '</style>';
        let hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'advancedcontent';
        hiddenInput.value = gjsContent;
        form.appendChild(hiddenInput);
      }
    });

    // Editing a page that's already Advanced should open straight into the
    // GrapesJS editor instead of showing the Basic view first.
    const advancedCheckbox = document.getElementById('advanced');
    if (advancedCheckbox && advancedCheckbox.checked) {
      document.getElementById('showEditorBtn').click();
    }
  });