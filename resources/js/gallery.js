// resources/js/gallery.js
(function () {
  const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

  function initGallery(root) {
    const track = root.querySelector('[data-track]');
    const dotsWrap = root.querySelector('[data-dots]');
    const prevBtn = root.querySelector('.gal-prev');
    const nextBtn = root.querySelector('.gal-next');
    if (!track) return;

    const slides = qsa('.gal-slide', track);
    const imgs = qsa('.gal-slide .gal-img', track);
    let index = 0;
    let slideW = () => root.clientWidth;
    let maxVH = () => Math.round(window.innerHeight * 0.65);

    // Crée les dots
    dotsWrap.innerHTML = '';
    const dots = slides.map((_, i) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'gal-dot';
      b.setAttribute('aria-label', `Aller à l’image ${i + 1}`);
      b.addEventListener('click', () => goTo(i, true));
      dotsWrap.appendChild(b);
      return b;
    });

    function markActive(i) {
      dots.forEach((d, k) => d.classList.toggle('is-active', k === i));
    }

    // Hauteur dynamique selon le ratio de l’image active
    function fitHeight(i) {
      const img = imgs[i];
      if (!img || !img.complete || !img.naturalWidth || !img.naturalHeight) return;
      const ratio = img.naturalWidth / img.naturalHeight; // w/h
      const w = slideW();
      const hFromWidth = Math.round(w / ratio); // hauteur idéale si image prend toute la largeur
      const h = clamp(hFromWidth, 260, maxVH()); // bornes mini/max
      root.style.setProperty('--gal-h', `${h}px`);
    }

    // Aller à l’index i
    function goTo(i, smooth = true) {
      index = clamp(i, 0, slides.length - 1);
      const x = Math.round(index * (slideW() + 8 /* gap approx */));
      track.scrollTo({ left: x, behavior: smooth ? 'smooth' : 'auto' });
      markActive(index);
      fitHeight(index);
      updateArrows();
    }

    function updateIndexFromScroll() {
      const w = slideW() + 8;
      index = clamp(Math.round(track.scrollLeft / w), 0, slides.length - 1);
      markActive(index);
      fitHeight(index);
      updateArrows();
    }

    function updateArrows() {
      if (prevBtn) prevBtn.disabled = (index === 0);
      if (nextBtn) nextBtn.disabled = (index === slides.length - 1);
    }

    // Flèches
    prevBtn && prevBtn.addEventListener('click', () => goTo(index - 1));
    nextBtn && nextBtn.addEventListener('click', () => goTo(index + 1));

    // Scroll → calcul index (debounce léger)
    let t;
    track.addEventListener('scroll', () => {
      clearTimeout(t);
      t = setTimeout(updateIndexFromScroll, 50);
    });

    // Drag souris / touch
    let isDown = false, startX = 0, startScroll = 0;
    const start = (e) => {
      isDown = true;
      startX = (e.touches ? e.touches[0].clientX : e.clientX);
      startScroll = track.scrollLeft;
    };
    const move = (e) => {
      if (!isDown) return;
      const x = (e.touches ? e.touches[0].clientX : e.clientX);
      track.scrollLeft = startScroll - (x - startX);
    };
    const end = () => {
      if (!isDown) return;
      isDown = false;
      // snap au plus proche
      updateIndexFromScroll();
      goTo(index);
    };
    track.addEventListener('pointerdown', start);
    track.addEventListener('pointermove', move);
    window.addEventListener('pointerup', end, { passive: true });
    track.addEventListener('touchstart', start, { passive: true });
    track.addEventListener('touchmove', move, { passive: true });
    track.addEventListener('touchend', end);

    // Clavier
    root.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') { e.preventDefault(); goTo(index - 1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); goTo(index + 1); }
    });
    root.tabIndex = 0; // focusable

    // Lightbox (zoom plein écran)
    const lb = document.getElementById('lightbox');
    const lbImg = document.getElementById('lightboxImg');
    const lbClose = lb ? lb.querySelector('.gal-lightbox-close') : null;

    function openLightbox(src, alt) {
      if (!lb || !lbImg) return;
      lbImg.src = src; lbImg.alt = alt || '';
      lb.removeAttribute('hidden');
    }
    function closeLightbox() {
      if (!lb || !lbImg) return;
      lb.setAttribute('hidden', '');
      lbImg.src = '';
    }
    lb && lb.addEventListener('click', (e) => {
      // fermer si on clique l’arrière-plan ou le bouton
      if (e.target === lb || e.target === lbImg || e.target === lbClose) closeLightbox();
    });
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeLightbox();
    });

    // Clic image → zoom
    slides.forEach((slide) => {
      slide.addEventListener('click', () => {
        const img = slide.querySelector('img');
        const z = slide.dataset.zoom || (img ? img.src : '');
        openLightbox(z, img ? img.alt : '');
      });
    });

    // Attendre que les images soient prêtes pour la 1re hauteur
    let loaded = 0;
    imgs.forEach((img, i) => {
      if (img.complete && img.naturalWidth) {
        loaded++; if (i === 0) fitHeight(0);
      } else {
        img.addEventListener('load', () => { loaded++; if (i === 0) fitHeight(0); }, { once: true });
        img.addEventListener('error', () => { loaded++; if (i === 0) root.style.setProperty('--gal-h', '420px'); }, { once: true });
      }
    });

    // Recalcul on resize / orientation
    window.addEventListener('resize', () => fitHeight(index));
    window.addEventListener('orientationchange', () => setTimeout(() => fitHeight(index), 50));

    // Go first
    goTo(0, false);
  }

  // Init toutes les galeries de la page
  window.addEventListener('DOMContentLoaded', () => {
    qsa('[data-gallery]').forEach(initGallery);
  });
})();
