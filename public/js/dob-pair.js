/* Mount Heaven — paired BS ⇄ AD date-of-birth fields (admission form). */
(function () {
  'use strict';

  var bsInput = document.getElementById('dob_bs');
  var adInput = document.getElementById('dob');
  var hint = document.getElementById('dobHint');
  var npLabel = document.getElementById('dobNpLabel');
  var form = bsInput && bsInput.closest('form');

  if (!bsInput || !adInput || !hint) return;

  var HINT_DEFAULT = hint.textContent;
  var lock = null; // 'bs' while typing BS, 'ad' while typing AD

  function debounce(fn, ms) {
    var t = null;
    return function () {
      var args = arguments, self = this;
      clearTimeout(t);
      t = setTimeout(function () { fn.apply(self, args); }, ms);
    };
  }

  function setIsBusy(el, busy) {
    el.classList.toggle('is-busy', !!busy);
  }

  function setHint(msg, isError) {
    hint.textContent = msg;
    hint.classList.toggle('dob-hint-error', !!isError);
  }

  function convert(from, value, targetInput) {
    if (!/^\d{4}[-/]\d{1,2}[-/]\d{1,2}$/.test(value)) return;
    setIsBusy(targetInput, true);

    var url = window.APP_BASE_URL + '/calendar/convert?from=' + from + '&date=' + encodeURIComponent(value);
    fetch(url, { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        setIsBusy(targetInput, false);
        // Ignore stale responses (user kept typing).
        if (lock && lock !== from) return;

        if (data.ok) {
          targetInput.value = from === 'bs' ? data.ad : data.bs;
          if (npLabel) npLabel.value = data.np_label || '';
          setHint(
            from === 'bs'
              ? ('✔ ' + (data.np_label || '') + ' = ' + (data.en_label || data.ad))
              : ('✔ ' + (data.en_label || data.bs) + ' = ' + (data.np_label || '')),
            false
          );
          targetInput.classList.remove('is-invalid');
        } else {
          if (npLabel) npLabel.value = '';
          setHint(data.error || 'Conversion failed.', true);
          targetInput.classList.add('is-invalid');
        }
      })
      .catch(function () {
        setIsBusy(targetInput, false);
        setHint('Could not convert right now — you can still fill the other date by hand.', true);
      });
  }

  var convertBs = debounce(function () { convert('bs', bsInput.value, adInput); }, 350);
  var convertAd = debounce(function () { convert('ad', adInput.value, bsInput); }, 350);

  bsInput.addEventListener('input', function () {
    lock = 'bs';
    this.classList.remove('is-invalid');
    convertBs();
  });

  adInput.addEventListener('input', function () {
    lock = 'ad';
    this.classList.remove('is-invalid');
    convertAd();
  });

  // Accept Devanagari digits in the BS field: convert them to ASCII as typed.
  bsInput.addEventListener('input', function () {
    if (/[\u0966-\u096F]/.test(this.value)) {
      this.value = this.value.replace(/[\u0966-\u096F]/g, function (ch) {
        return String.fromCharCode(ch.charCodeAt(0) - 0x966);
      });
    }
  });

  // After a validation-error reload, fill the missing side automatically.
  if (bsInput.value && !adInput.value) convert('bs', bsInput.value, adInput);
  else if (!bsInput.value && adInput.value) convert('ad', adInput.value, bsInput);

  /* ---------- BS date picker popup (native-picker feel for the B.S. field) ---------- */
  (function () {
    var picker = null;
    var view = { y: 0, m: 0 };      // month currently shown in the popup
    var weekHeader = ['सोम', 'मंगल', 'बुध', 'बिही', 'शुक्र', 'शनि', 'आइत'];
    var NP_MONTHS = ['बैशाख', 'जेठ', 'असार', 'साउन', 'भदौ', 'असोज', 'कार्तिक', 'मंसिर', 'पुष', 'माघ', 'फाल्गुण', 'चैत्र'];
    var digitMap = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
    var npNum = function (n) {
      return String(n).replace(/\d/g, function (d) { return digitMap[+d]; });
    };

    function todayBsParts() {
      var n = new Date();
      var iso = n.getFullYear() + '-' + String(n.getMonth() + 1).padStart(2, '0') + '-' + String(n.getDate()).padStart(2, '0');
      var parts = (document.getElementById('bsToday') || {}).value || '';
      return parts ? parts.split('-').map(Number) : null;
    }

    function build() {
      picker = document.createElement('div');
      picker.className = 'bs-picker';
      picker.hidden = true;
      picker.setAttribute('role', 'dialog');
      picker.setAttribute('aria-label', 'Choose Nepali date');
      document.body.appendChild(picker);

      picker.addEventListener('click', function (e) {
        if (e.target === picker) close();
      });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !picker.hidden) close();
      });
    }

    function open() {
      if (!picker) build();
      var initial = bsInput.value.match(/^(\d{4})[-/](\d{1,2})/);
      if (initial) {
        view.y = +initial[1]; view.m = +initial[2];
      } else {
        var t = todayBsParts();
        view.y = t ? t[0] : 2083; view.m = t ? t[1] : 6;
      }
      picker.hidden = false;
      render();
    }

    function close() {
      if (picker) picker.hidden = true;
    }

    function render() {
      picker.innerHTML = '<div class="bs-picker-loading">Loading…</div>';
      fetch(window.APP_BASE_URL + '/calendar/month?y=' + view.y + '&m=' + view.m)
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (!data.ok) { picker.innerHTML = '<div class="bs-picker-error">' + data.error + '</div>'; return; }
          draw(data);
        })
        .catch(function () {
          picker.innerHTML = '<div class="bs-picker-error">Could not load calendar.</div>';
        });
    }

    function draw(data) {
      var html = '';
      html += '<div class="bs-picker-head">';
      html += '<button type="button" class="bs-picker-nav" data-nav="prev" aria-label="Previous month">‹</button>';
      html += '<strong>' + data.monthName + ' ' + data.yearNp + '</strong>';
      html += '<button type="button" class="bs-picker-nav" data-nav="next" aria-label="Next month">›</button>';
      html += '</div>';
      html += '<div class="bs-picker-week">';
      weekHeader.forEach(function (w) { html += '<span>' + w + '</span>'; });
      html += '</div>';
      html += '<div class="bs-picker-grid">';
      var lead = (data.leading + 6) % 7; // Sunday-first → Monday-first
      for (var i = 0; i < lead; i++) html += '<span class="bs-picker-cell is-blank"></span>';
      data.days.forEach(function (d) {
        html += '<button type="button" class="bs-picker-cell' + (d.today ? ' is-today' : '') +
                '" data-day="' + d.d + '" data-ad="' + d.ad + '" title="AD ' + d.ad + '">' + npNum(d.d) + '</button>';
      });
      html += '</div>';
      html += '<div class="bs-picker-foot">';
      html += '<button type="button" class="bs-picker-today" data-nav="today">Today</button>';
      html += '<button type="button" class="bs-picker-close" data-nav="close">Done</button>';
      html += '</div>';
      picker.innerHTML = html;

      picker.querySelectorAll('[data-nav]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var nav = this.getAttribute('data-nav');
          if (nav === 'prev') { view.m--; if (view.m < 1) { view.m = 12; view.y--; } render(); }
          else if (nav === 'next') { view.m++; if (view.m > 12) { view.m = 1; view.y++; } render(); }
          else if (nav === 'today') {
            var t = todayBsParts();
            if (t) { view.y = t[0]; view.m = t[1]; render(); } else { close(); }
          }
          else if (nav === 'close') close();
        });
      });

      picker.querySelectorAll('.bs-picker-cell[data-day]').forEach(function (cell) {
        cell.addEventListener('click', function () {
          var d = this.getAttribute('data-day');
          var ad = this.getAttribute('data-ad');
          bsInput.value = view.y + '-' + String(view.m).padStart(2, '0') + '-' + String(d).padStart(2, '0');
          if (ad) adInput.value = ad;
          bsInput.classList.remove('is-invalid');
          setHint('✔ ' + npNum(view.y) + '-' + npNum(view.m) + '-' + npNum(d) + ' selected', false);
          close();
          // Fire the normal conversion for the confirmation line.
          convert('bs', bsInput.value, adInput);
        });
      });
    }

    function position() {
      var r = bsInput.getBoundingClientRect();
      picker.style.position = 'fixed';
      picker.style.left = Math.max(8, Math.min(r.left, window.innerWidth - picker.offsetWidth - 8)) + 'px';
      var top = r.bottom + 6;
      if (top + picker.offsetHeight > window.innerHeight - 8) top = Math.max(8, r.top - picker.offsetHeight - 6);
      picker.style.top = top + 'px';
    }

    // Picker toggle icon inside the BS field (like the native date icon).
    var wrap = document.createElement('span');
    wrap.className = 'bs-picker-toggle';
    wrap.innerHTML = '📅';
    wrap.setAttribute('role', 'button');
    wrap.setAttribute('tabindex', '0');
    wrap.setAttribute('aria-label', 'Open Nepali calendar');
    bsInput.parentNode.style.position = 'relative';
    bsInput.parentNode.appendChild(wrap);

    function toggle() {
      if (!picker || picker.hidden) { open(); requestAnimationFrame(position); }
      else close();
    }
    wrap.addEventListener('click', toggle);
    wrap.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
    });
    document.addEventListener('click', function (e) {
      if (picker && !picker.hidden && !picker.contains(e.target) && e.target !== wrap && !wrap.contains(e.target)) close();
    });
    window.addEventListener('resize', function () { if (picker && !picker.hidden) position(); });
  })();

  // Server-side only stores the AD value; make sure a filled BS date with an
  // empty AD field still submits correctly by syncing one last time.
  if (form) {
    form.addEventListener('submit', function (e) {
      if (bsInput.value && !adInput.value) {
        e.preventDefault();
        convert('bs', bsInput.value, adInput);
        // Retry once the async conversion fills the AD input.
        setTimeout(function () { form.submit(); }, 600);
      }
    });
  }
})();
