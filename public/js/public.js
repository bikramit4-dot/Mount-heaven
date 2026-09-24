/* Mount Heaven — public site scripts */
(function () {
  'use strict';

  /* Mobile nav */
  var toggle = document.getElementById('navToggle');
  var links = document.getElementById('navLinks');
  if (toggle && links) {
    toggle.addEventListener('click', function () {
      var isOpen = links.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    links.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        links.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* Keep the reference-style header background while scrolling. */
  var siteHeader = document.querySelector('.site-header');
  if (siteHeader) {
    var updateHeader = function () { siteHeader.classList.toggle('is-scrolled', window.scrollY > 8); };
    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive: true });
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

  /* Popup banner (home page): auto-shows when the site opens, auto-hides
     after the admin-set duration. An ✕ button closes it any time. */
  var popup = document.getElementById('noticePopup');
  if (popup) {
    var POPUP_KEY = 'popupSeen-' + popup.getAttribute('data-notice-id');
    var autoTimer = null;

    var openPopup = function () {
      popup.hidden = false;
      var secs = parseInt(popup.getAttribute('data-duration') || '5', 10);
      autoTimer = setTimeout(closePopup, secs * 1000);
    };

    var closePopup = function () {
      if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }
      popup.hidden = true;
      try { sessionStorage.setItem(POPUP_KEY, '1'); } catch (e) { /* private mode */ }
    };

    var seen = false;
    try { seen = sessionStorage.getItem(POPUP_KEY) === '1'; } catch (e) {}
    if (!seen) setTimeout(openPopup, 800);

    document.getElementById('noticePopupClose').addEventListener('click', closePopup);
    popup.addEventListener('click', function (e) {
      if (e.target === popup) closePopup();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !popup.hidden) closePopup();
    });

    /* Clicking the banner image opens its link (if the admin set one). */
    var img = popup.querySelector('img');
    if (img) {
      img.addEventListener('click', function () {
        var link = popup.getAttribute('data-link');
        if (link) window.location.href = link;
      });
    }
  }

  /* Reveal-on-scroll animation.
     The hidden state is applied here (not in CSS) so content is NEVER
     invisible when JS is disabled, blocked or served stale from cache —
     worst case, sections simply show without the fade-in effect. */
  var revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length && 'IntersectionObserver' in window) {
    revealEls.forEach(function (el) { el.classList.add('js-reveal', 'reveal-hidden'); });
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('reveal-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach(function (el) { revealObserver.observe(el); });
  }

  /* Stats strip: reveal on scroll + count-up numbers */
  function countUp(el) {
    var target = String(el.getAttribute('data-countup') || '');
    var match = target.match(/^(\D*)([\d,]+)(.*)$/); // prefix, number, suffix
    if (!match) return;
    var prefix = match[1];
    var end = parseInt(match[2].replace(/,/g, ''), 10);
    var suffix = match[3];
    var dur = 1400;
    var start = null;

    function frame(ts) {
      if (start === null) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3); // ease-out cubic
      el.textContent = prefix + Math.round(end * eased).toLocaleString('en-IN') + suffix;
      if (p < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  var strip = document.getElementById('statsStrip');
  if (strip) {
    var items = strip.querySelectorAll('.strip-item');
    var counted = false;

    function reveal() {
      items.forEach(function (item) { item.classList.add('visible'); });
      if (!counted) {
        counted = true;
        strip.querySelectorAll('[data-countup]').forEach(countUp);
      }
    }

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            reveal();
            observer.disconnect();
          }
        });
      }, { threshold: 0.3 });
      observer.observe(strip);
      // safety: reveal anyway if not intersected within 2.5s
      setTimeout(function () {
        if (!counted) reveal();
      }, 2500);
    } else {
      reveal();
    }
  }

  /* About badge: count up the numbers when it scrolls into view */
  var badge = document.querySelector('.about-badge');
  if (badge) {
    var nums = badge.querySelectorAll('[data-countup]');
    if ('IntersectionObserver' in window) {
      var badgeObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            nums.forEach(countUp);
            badgeObserver.disconnect();
          }
        });
      }, { threshold: 0.3 });
      badgeObserver.observe(badge);
    } else {
      nums.forEach(countUp);
    }
  }
})();
