document.getElementById('showEditorBtn').addEventListener('click', function () {
  // UI adjustments
  document.getElementById('basic-container').style.display = 'none';
  document.getElementById('gjs-container').style.display = 'block';
  this.style.display = 'none';

  // Load GrapesJS CSS
  const grapesCss = document.createElement('link');
  grapesCss.rel = 'stylesheet';
  grapesCss.href = pluginBasePath + 'grapes.min.css';
  document.head.appendChild(grapesCss);

  // Load GrapesJS core script
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

// GrapesJS initialization function
function initGrapesEditor() {
  const editor = grapesjs.init({
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
  });
}

const page = {
  submit: function(e) {
    e.preventDefault();
    const form = e.currentTarget;
    const inputs = form.querySelectorAll("input,select,textarea");
    document.querySelector('.progress').classList.remove('d-none');
    let required = [];
    const myform = new FormData();

    for (const input of inputs) {
      const { type, name, value, checked, files } = input;

      switch (type) {
        case "file":
          if (files.length > 0) {
            myform.append(name, files[0]);
          }
          break;
        case "checkbox":
        case "radio":
          if (checked) {
            myform.append(name, value);
          }
          break;
        default:
          if(name == 'title' && value.trim() == '')
          {
            alert("A title is required!");
            return;
          }

          myform.append(name, value);
          break;
      }
    }

    page.uploading = true;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          page.handleResult(xhr.responseText);
        } else {
          alert("Failed to submit form: " + xhr.statusText);
        }
      }
    };

    xhr.upload.addEventListener('progress', function(e) {
      if (e.lengthComputable) {
        let percent = Math.round((e.loaded / e.total) * 100);
        let progBar = document.querySelector('.progress-bar');
        progBar.style.width = percent + '%';
        progBar.setAttribute('aria-valuenow', percent);
        progBar.textContent = 'Saving... ' + percent + '%';
      }
    });  

    xhr.onerror = function() {
      alert("Network error during form submission.");
    };

    //xhr.open('POST', form.action || window.location.href, true);
    xhr.open('POST', '', true);
    xhr.send(myform);
  },

  handleResult: function(result) {
    let data = JSON.parse(result);
    if (typeof data == 'object') {
      alert(data.message);
      if (data.success) {
        window.location.href = pluginBasePath; // Redirect on success
      } else {
        for (let key in data.errors) {
          alert(data.errors[key]); // Display errors
        }
      }
    } else {
      console.log(result); // Fallback if not JSON
    }
  },
};

// Trigger form submission when 'Save Page' button is clicked
document.getElementById('savePageBtn').addEventListener('click', function() {
  document.getElementById('pageForm').submit();
});