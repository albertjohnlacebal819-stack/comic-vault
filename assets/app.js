document.querySelectorAll('.needs-validation').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    if (!form.checkValidity()) e.preventDefault();
    form.classList.add('was-validated');
  });
});

document.querySelectorAll('form.js-confirm').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    if (!confirm(form.dataset.msg || 'Are you sure?')) e.preventDefault();
  });
});

var input = document.getElementById('coverInput');
if (input) {
  input.addEventListener('change', function () {
    var img = document.getElementById('coverPreview');
    var file = input.files[0];
    if (file && file.type.indexOf('image/') === 0) {
      img.src = URL.createObjectURL(file);
      img.classList.remove('d-none');
    } else {
      img.classList.add('d-none');
    }
  });
}
