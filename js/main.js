/* ==============================================
   LAURA QUINTERO CASA SPA — Main JavaScript
   ============================================== */

document.addEventListener('DOMContentLoaded', () => {

  // ── AOS Init ──────────────────────────────────
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 700, once: true, offset: 60 });
  }

  // ── Page Loader ───────────────────────────────
  const loader = document.getElementById('page-loader');
  if (loader) {
    window.addEventListener('load', () => {
      setTimeout(() => loader.classList.add('hidden'), 600);
    });
    // Fallback
    setTimeout(() => loader.classList.add('hidden'), 3000);
  }

  // ── Navbar scroll effect ──────────────────────
  const navbar = document.getElementById('navbar');
  const handleScroll = () => {
    if (window.scrollY > 40) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');
    highlightNav();
  };
  window.addEventListener('scroll', handleScroll, { passive: true });

  // ── Hamburger menu ────────────────────────────
  const hamburger = document.getElementById('hamburger');
  const mainNav = document.getElementById('main-nav');
  hamburger?.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    mainNav.classList.toggle('open');
    document.body.style.overflow = mainNav.classList.contains('open') ? 'hidden' : '';
  });
  // Close on nav link click
  mainNav?.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      hamburger.classList.remove('open');
      mainNav.classList.remove('open');
      document.body.style.overflow = '';
    });
  });

  // ── Active nav highlight on scroll ───────────
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('#main-nav ul li a');
  function highlightNav() {
    const scrollY = window.scrollY + 120;
    sections.forEach(sec => {
      if (scrollY >= sec.offsetTop && scrollY < sec.offsetTop + sec.offsetHeight) {
        navLinks.forEach(l => l.classList.remove('active'));
        const active = document.querySelector(`#main-nav a[href="#${sec.id}"]`);
        if (active) active.classList.add('active');
      }
    });
  }

  // ── Render Productos ──────────────────────────
  const productsGrid = document.getElementById('products-grid');
  if (productsGrid && typeof PRODUCTS !== 'undefined') {
    productsGrid.innerHTML = PRODUCTS.map((p, i) => `
      <div class="product-card" data-aos="fade-up" data-aos-delay="${i * 80}">
        <div class="product-photo"><img src="${p.foto}" alt="${p.nombre}" loading="lazy" /></div>
        <div class="product-body">
          <h3>${p.nombre}</h3>
          <p>${p.descripcion}</p>
          <div class="product-footer">
            <span class="product-price">${p.precio}</span>
            <a href="https://wa.me/573147388239?text=${encodeURIComponent('Hola, quiero más información sobre ' + p.nombre)}" target="_blank" class="product-link">
              <i class="fab fa-whatsapp"></i> Pedir
            </a>
          </div>
        </div>
      </div>
    `).join('');
    if (typeof AOS !== 'undefined') AOS.refresh();
  }

  // ── Hero Slider ───────────────────────────────
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.slider-dots .dot');
  let currentSlide = 0;
  let sliderInterval;

  function goToSlide(idx) {
    slides[currentSlide].classList.remove('active');
    dots[currentSlide]?.classList.remove('active');
    currentSlide = (idx + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    dots[currentSlide]?.classList.add('active');
  }

  function startSlider() {
    sliderInterval = setInterval(() => goToSlide(currentSlide + 1), 5000);
  }
  function resetSlider() {
    clearInterval(sliderInterval);
    startSlider();
  }

  document.querySelector('.slider-btn.prev')?.addEventListener('click', () => { goToSlide(currentSlide - 1); resetSlider(); });
  document.querySelector('.slider-btn.next')?.addEventListener('click', () => { goToSlide(currentSlide + 1); resetSlider(); });
  dots.forEach(dot => {
    dot.addEventListener('click', () => { goToSlide(parseInt(dot.dataset.index)); resetSlider(); });
  });
  if (slides.length > 1) startSlider();

  // ── Service Tabs ──────────────────────────────
  const tabBtns = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');
  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      tabBtns.forEach(b => b.classList.remove('active'));
      tabContents.forEach(c => c.classList.remove('active'));
      btn.classList.add('active');
      const target = document.getElementById(`tab-${btn.dataset.tab}`);
      if (target) target.classList.add('active');
      // Refresh AOS for newly visible elements
      if (typeof AOS !== 'undefined') AOS.refresh();
    });
  });

  // ── Price List Modal ──────────────────────────
  const priceModal = document.getElementById('price-modal');
  const openPriceList = document.getElementById('open-price-list');
  const priceModalClose = document.getElementById('price-modal-close');

  function openPriceModal() {
    priceModal?.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closePriceModal() {
    priceModal?.classList.remove('open');
    document.body.style.overflow = '';
  }
  openPriceList?.addEventListener('click', openPriceModal);
  priceModalClose?.addEventListener('click', closePriceModal);
  priceModal?.addEventListener('click', e => { if (e.target === priceModal) closePriceModal(); });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && priceModal?.classList.contains('open')) closePriceModal();
  });

  // ── Lightbox Gallery ─────────────────────────
  const galleryItems = document.querySelectorAll('.gallery-item');
  const lightbox = document.getElementById('lightbox');
  const lbImg = document.getElementById('lightbox-img');
  const lbClose = document.getElementById('lightbox-close');
  const lbPrev = document.getElementById('lightbox-prev');
  const lbNext = document.getElementById('lightbox-next');
  const galleryImages = [];
  let lbIndex = 0;

  galleryItems.forEach((item, i) => {
    const img = item.querySelector('img');
    galleryImages.push({ src: img.src, alt: img.alt });
    item.addEventListener('click', () => openLightbox(i));
  });

  function openLightbox(idx) {
    lbIndex = idx;
    lbImg.src = galleryImages[lbIndex].src;
    lbImg.alt = galleryImages[lbIndex].alt;
    lightbox.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeLightbox() {
    lightbox.classList.remove('open');
    document.body.style.overflow = '';
  }
  lbClose?.addEventListener('click', closeLightbox);
  lightbox?.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });
  lbPrev?.addEventListener('click', () => { lbIndex = (lbIndex - 1 + galleryImages.length) % galleryImages.length; lbImg.src = galleryImages[lbIndex].src; lbImg.alt = galleryImages[lbIndex].alt; });
  lbNext?.addEventListener('click', () => { lbIndex = (lbIndex + 1) % galleryImages.length; lbImg.src = galleryImages[lbIndex].src; lbImg.alt = galleryImages[lbIndex].alt; });
  document.addEventListener('keydown', e => {
    if (!lightbox?.classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') lbPrev?.click();
    if (e.key === 'ArrowRight') lbNext?.click();
  });

  // Touch swipe for lightbox
  let lbTouchStart = 0;
  lightbox?.addEventListener('touchstart', e => { lbTouchStart = e.touches[0].clientX; }, { passive: true });
  lightbox?.addEventListener('touchend', e => {
    const diff = lbTouchStart - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) diff > 0 ? lbNext?.click() : lbPrev?.click();
  });

  // ── FAQ Accordion ─────────────────────────────
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(item => {
    const btn = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');
    btn?.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      faqItems.forEach(i => {
        i.classList.remove('open');
        const a = i.querySelector('.faq-answer');
        if (a) a.style.maxHeight = '0';
      });
      if (!isOpen) {
        item.classList.add('open');
        if (answer) answer.style.maxHeight = answer.scrollHeight + 'px';
      }
    });
  });

  // ── Smooth scroll for anchor links ───────────
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const offset = navbar ? navbar.offsetHeight + 8 : 80;
        window.scrollTo({ top: target.offsetTop - offset, behavior: 'smooth' });
      }
    });
  });

});
