(function () {
  'use strict';

  function initSlider(root) {
    if (!root || root.dataset.psfReady === '1') return;
    root.dataset.psfReady = '1';

    const track = root.querySelector('.psf__track');
    const cards = Array.from(root.querySelectorAll('.psf-card'));
    const tabs = Array.from(root.querySelectorAll('.psf__tab'));
    const prev = root.querySelector('.psf__arrow--prev');
    const next = root.querySelector('.psf__arrow--next');
    const empty = root.querySelector('.psf__no-results');
    const motion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth';
    let filter = root.dataset.defaultFilter || 'sale';

    function visibleCards() {
      return cards.filter(card => !card.hidden);
    }

    function applyFilter(newFilter) {
      filter = newFilter || 'all';
      cards.forEach(card => {
        card.hidden = filter !== 'all' && card.dataset.listingType !== filter;
      });

      tabs.forEach(tab => {
        const active = tab.dataset.filter === filter;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-pressed', active ? 'true' : 'false');
      });

      if (empty) empty.hidden = visibleCards().length !== 0;
      track.scrollTo({ left: 0, behavior: 'instant' });
      updateArrows();
    }

    function scrollAmount() {
      const first = visibleCards()[0];
      if (!first) return Math.max(280, track.clientWidth * 0.8);
      const styles = window.getComputedStyle(track);
      const gap = parseFloat(styles.columnGap || styles.gap || 0) || 0;
      return (first.getBoundingClientRect().width + gap) * (styles.direction === 'rtl' ? -1 : 1);
    }

    function updateArrows() {
      if (!prev || !next) return;
      const max = Math.max(0, track.scrollWidth - track.clientWidth - 2);
      const position = Math.abs(track.scrollLeft);
      prev.disabled = position <= 2;
      next.disabled = position >= max;
    }

    tabs.forEach(tab => tab.addEventListener('click', () => applyFilter(tab.dataset.filter)));
    if (prev) prev.addEventListener('click', () => track.scrollBy({ left: -scrollAmount(), behavior: motion() }));
    if (next) next.addEventListener('click', () => track.scrollBy({ left: scrollAmount(), behavior: motion() }));
    track.addEventListener('scroll', updateArrows, { passive: true });
    // Measure the widget rather than assuming a full-width Elementor column.
    // Avoid inline-size containment: nested flex widgets can reserve stale heights.
    function updateLayout() {
      const width = root.clientWidth;
      root.dataset.psfLayout = width >= 1100 ? 'desktop' : width >= 640 ? 'tablet' : 'compact';
      updateArrows();
    }
    if (window.ResizeObserver) new ResizeObserver(updateLayout).observe(root);
    else window.addEventListener('resize', updateLayout, { passive: true });
    updateLayout();

    root.querySelectorAll('.psf-card__save').forEach(button => {
      const card = button.closest('.psf-card');
      const key = 'psf-saved-' + (card ? card.dataset.propertyId : '');
      let saved = false;
      try { saved = localStorage.getItem(key) === '1'; } catch (e) {}

      function paint() {
        button.classList.toggle('is-saved', saved);
        button.setAttribute('aria-pressed', saved ? 'true' : 'false');
      }

      button.addEventListener('click', () => {
        saved = !saved;
        try {
          if (saved) localStorage.setItem(key, '1');
          else localStorage.removeItem(key);
        } catch (e) {}
        paint();
      });
      paint();
    });

    if (!tabs.length && filter !== 'all') {
      applyFilter(filter);
    } else if (tabs.some(tab => tab.dataset.filter === filter)) {
      applyFilter(filter);
    } else {
      applyFilter('all');
    }
  }

  function boot(context) {
    (context || document).querySelectorAll('.psf').forEach(initSlider);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => boot(document));
  else boot(document);

  function registerElementor() {
    if (window.elementorFrontend && window.elementorFrontend.hooks) {
      window.elementorFrontend.hooks.addAction('frontend/element_ready/psf_property_slider.default', function ($scope) {
        const el = $scope && $scope[0] ? $scope[0] : document;
        boot(el);
      });
    }
  }

  // Elementor dispatches this event through jQuery, including editor rerenders.
  if (window.jQuery) window.jQuery(window).on('elementor/frontend/init', registerElementor);
  registerElementor();
})();
