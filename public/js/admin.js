/* Mount Heaven — admin panel scripts */
(function () {
  'use strict';

  /* Sidebar (mobile) */
  var burger = document.getElementById('adminBurger');
  var sidebar = document.getElementById('sidebar');
  if (burger && sidebar) {
    burger.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  /* Confirmation dialogs */
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('[data-confirm]');
    if (form && !window.confirm(form.getAttribute('data-confirm'))) {
      e.preventDefault();
    }
  });

  /* Password visibility toggles */
  document.querySelectorAll('.pw-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.getAttribute('data-target'));
      if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
      }
    });
  });

  /* Gallery: route the upload form to the selected album */
  var uploadForm = document.getElementById('photoUploadForm');
  var albumSelect = document.getElementById('up_album');
  if (uploadForm && albumSelect) {
    uploadForm.addEventListener('submit', function () {
      uploadForm.action = uploadForm.action.replace(/\/albums\/\d+\/photos$/, '/albums/' + albumSelect.value + '/photos');
    });
  }

  /* Auto-dismiss success alerts */
  document.querySelectorAll('.alert-success').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .6s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 700);
    }, 5000);
  });
})();
