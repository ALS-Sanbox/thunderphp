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
  });