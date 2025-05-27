  document.getElementById('description').addEventListener('input', function () {
    const maxLength = 155;
    const currentLength = this.value.length;
    const counter = document.getElementById('descCounter');

    counter.textContent = `${currentLength} / ${maxLength} characters`;

    if (currentLength > maxLength) {
      counter.classList.remove('text-muted');
      counter.classList.add('text-danger');
    } else {pluginBasePath
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
    grapesCss.href = pluginBasePath + 'grapes.min.css';
    document.head.appendChild(grapesCss);

    const grapesScript = document.createElement('script');
    grapesScript.src = pluginBasePath + 'grapes.min.js';
    grapesScript.onload = function () {
      const pluginScripts = [
        'grapesjs-blocks-basic.min.js',
        'grapesjs-preset-webpage.min.js',
        'grapesjs-plugin-forms.min.js',
        'grapesjs-navbar.min.js',
      ];

      let loadedPlugins = 0;

      pluginScripts.forEach((plugin) => {
        const pluginScript = document.createElement('script');
        pluginScript.src = pluginBasePath + plugin;
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

  function initGrapesEditor() {
    grapesEditor = grapesjs.init({
      container: '#gjs',
      fromElement: true,
      height: '100vh',
      width: 'auto',
      plugins: [
        'gjs-blocks-basic',
        'grapesjs-preset-webpage',
        'grapesjs-plugin-forms',
        'grapesjs-navbar',
      ],
    assets: {
      storageType: 'self',
      upload: editImagePath, // e.g. '/admin/plugin/basicpages/upload'
      uploadName: 'files',
      onUpload: async ({ files }) => {
        const body = new FormData();
        for (const file of files) {
          body.append('files[]', file);
        }

        const response = await fetch(editImagePath, {
          method: 'POST',
          body,
        });

        const result = await response.json();
        return result.data; // should be [{src: 'url1'}, {src: 'url2'}, ...]
      },
    }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {

    $('.summernote').summernote({
      placeholder: 'Hello put content here',
      tabsize: 2,
      height: 600
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
  });