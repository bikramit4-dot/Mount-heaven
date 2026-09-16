/* Mount Heaven — public site scripts */
(function () {
  'use strict';

  /* Mobile nav */
  var toggle = document.getElementById('navToggle');
  var links = document.getElementById('navLinks');
  if (toggle && links) {
    toggle.addEventListener('click', function () {
      links.classList.toggle('open');
    });
  }

  /* Hero slider */
  var slides = document.querySelectorAll('#heroSlides .hero-slide');
  var dots = document.querySelectorAll('#heroDots button');
  var current = 0;
  var timer = null;

  function showSlide(index) {
    slides.forEach(function (s, i) { s.classList.toggle('is-active', i === index); });
    dots.forEach(function (d, i) { d.classList.toggle('is-active', i === index); });
    current = index;
  }

  function startAuto() {
    stopAuto();
    if (slides.length > 1) {
      timer = setInterval(function () {
        showSlide((current + 1) % slides.length);
      }, 6000);
    }
  }
  function stopAuto() {
    if (timer) clearInterval(timer);
    timer = null;
  }

  dots.forEach(function (d, i) {
    d.addEventListener('click', function () {
      showSlide(i);
      startAuto();
    });
  });

  if (slides.length) startAuto();

  /* Gallery filter */
  var filterBar = document.getElementById('galleryFilter');
  var items = document.querySelectorAll('#galleryGrid .gallery-item');
  if (filterBar && items.length) {
    filterBar.addEventListener('click', function (e) {
      var btn = e.target.closest('.filter');
      if (!btn) return;
      filterBar.querySelectorAll('.filter').forEach(function (b) { b.classList.remove('is-active'); });
      btn.classList.add('is-active');
      var album = btn.getAttribute('data-album');
      items.forEach(function (item) {
        var match = album === 'all' || item.getAttribute('data-album') === album;
        item.style.display = match ? '' : 'none';
      });
    });
  }

  /* Lightbox */
  var lightbox = document.getElementById('lightbox');
  if (lightbox && items.length) {
    var lbImg = document.getElementById('lightboxImg');
    var lbCap = document.getElementById('lightboxCaption');
    var visible = [];

    function refreshVisible() {
      visible = Array.prototype.filter.call(items, function (i) { return i.style.display !== 'none'; });
    }

    function openAt(index) {
      refreshVisible();
      var item = visible[index];
      if (!item) return;
      var img = item.querySelector('img');
      lbImg.src = img.src;
      lbCap.textContent = item.querySelector('figcaption').textContent;
      lightbox.hidden = false;
      document.body.style.overflow = 'hidden';
      lightbox.setAttribute('data-index', index);
    }

    items.forEach(function (item) {
      item.addEventListener('click', function () {
        refreshVisible();
        openAt(visible.indexOf(item));
      });
    });

    function move(step) {
      var idx = parseInt(lightbox.getAttribute('data-index') || '0', 10);
      refreshVisible();
      openAt((idx + step + visible.length) % visible.length);
    }

    document.getElementById('lightboxClose').addEventListener('click', closeLightbox);
    document.getElementById('lightboxPrev').addEventListener('click', function () { move(-1); });
    document.getElementById('lightboxNext').addEventListener('click', function () { move(1); });
    document.addEventListener('keydown', function (e) {
      if (lightbox.hidden) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') move(-1);
      if (e.key === 'ArrowRight') move(1);
    });

    function closeLightbox() {
      lightbox.hidden = true;
      document.body.style.overflow = '';
    }
  }
})();
