const menuToggle = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('.main-nav');

if (menuToggle && mainNav) {
  menuToggle.addEventListener('click', () => {
    const isOpen = mainNav.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
    menuToggle.innerHTML = isOpen ? '<i data-lucide="x"></i>' : '<i data-lucide="menu"></i>';
    lucide.createIcons();
  });

  mainNav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      mainNav.classList.remove('open');
      menuToggle.setAttribute('aria-expanded', 'false');
      menuToggle.innerHTML = '<i data-lucide="menu"></i>';
      lucide.createIcons();
    });
  });
}

const flyers = [...document.querySelectorAll('.flyer')];
const flyerDots = [...document.querySelectorAll('.flyer-dot')];
const previousFlyer = document.querySelector('.flyer-prev');
const nextFlyer = document.querySelector('.flyer-next');
const flyerCarousel = document.querySelector('.flyer-carousel');
let currentFlyer = 0;
let flyerTimer;
let hoverDirection = 0;

const showFlyer = (index) => {
  currentFlyer = (index + flyers.length) % flyers.length;
  flyers.forEach((flyer, flyerIndex) => flyer.classList.toggle('active', flyerIndex === currentFlyer));
  flyerDots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === currentFlyer));
};

const restartFlyerTimer = () => {
  window.clearInterval(flyerTimer);
  flyerTimer = window.setInterval(() => showFlyer(currentFlyer + 1), 5000);
};

if (flyers.length) {
  previousFlyer.addEventListener('click', () => { showFlyer(currentFlyer - 1); restartFlyerTimer(); });
  nextFlyer.addEventListener('click', () => { showFlyer(currentFlyer + 1); restartFlyerTimer(); });
  flyerDots.forEach((dot) => dot.addEventListener('click', () => { showFlyer(Number(dot.dataset.target)); restartFlyerTimer(); }));
  flyerCarousel.addEventListener('mouseenter', () => window.clearInterval(flyerTimer));
  flyerCarousel.addEventListener('mousemove', (event) => {
    const position = (event.clientX - event.currentTarget.getBoundingClientRect().left) / event.currentTarget.offsetWidth;
    const direction = position < 0.3 ? -1 : position > 0.7 ? 1 : 0;

    if (direction && direction !== hoverDirection) {
      showFlyer(currentFlyer + direction);
    }
    hoverDirection = direction;
    if (direction) restartFlyerTimer();
  });
  flyerCarousel.addEventListener('mouseleave', () => {
    hoverDirection = 0;
    restartFlyerTimer();
  });
  restartFlyerTimer();
}

const galleryFiltersContainer = document.querySelector('.gallery-filters');
const galleryGrid = document.querySelector('.gallery-grid');
const lightbox = document.querySelector('.gallery-lightbox');
const lightboxImage = document.querySelector('.lightbox-image');
const lightboxCaption = document.querySelector('.lightbox-caption');
let galleryImages = [];
let currentImageIndex = 0;

const openLightbox = (index) => {
  currentImageIndex = (index + galleryImages.length) % galleryImages.length;
  const image = galleryImages[currentImageIndex];
  lightboxImage.src = image.src;
  lightboxImage.alt = image.alt;
  lightboxCaption.textContent = image.caption;
  lightbox.classList.add('is-open');
  lightbox.setAttribute('aria-hidden', 'false');
};

const closeLightbox = () => {
  lightbox.classList.remove('is-open');
  lightbox.setAttribute('aria-hidden', 'true');
};

const renderGallery = (groups) => {
  const entries = Object.entries(groups);
  galleryFiltersContainer.insertAdjacentHTML('beforeend', entries.map(([groupName]) => `<button class="gallery-filter" data-filter="${groupName}">${groupName}</button>`).join(''));
  galleryGrid.innerHTML = entries.flatMap(([groupName, images]) => images.map((image) => `<figure class="gallery-card" data-level="${groupName}"><button class="gallery-card-button" type="button" aria-label="Ver ${image.name}"><img src="${image.src}" alt="${image.name} · ${groupName}" loading="lazy"><figcaption><strong>${groupName}</strong><span>${image.name}</span></figcaption></button></figure>`)).join('');
  galleryImages = [...galleryGrid.querySelectorAll('.gallery-card')].map((card) => ({
    src: card.querySelector('img').src,
    alt: card.querySelector('img').alt,
    caption: `${card.dataset.level} · ${card.querySelector('img').alt.split(' · ')[0]}`,
  }));

  galleryFiltersContainer.querySelectorAll('.gallery-filter').forEach((filterButton) => {
    filterButton.addEventListener('click', () => {
      galleryFiltersContainer.querySelectorAll('.gallery-filter').forEach((button) => button.classList.toggle('active', button === filterButton));
      galleryGrid.querySelectorAll('.gallery-card').forEach((card) => card.classList.toggle('is-hidden', filterButton.dataset.filter !== 'todos' && card.dataset.level !== filterButton.dataset.filter));
    });
  });
  galleryGrid.querySelectorAll('.gallery-card-button').forEach((button, index) => button.addEventListener('click', () => openLightbox(index)));
};

if (galleryGrid) {
  fetch('gallery.json').then((response) => response.json()).then(renderGallery).catch(() => { galleryGrid.innerHTML = '<p class="gallery-status">No se pudieron cargar las fotografías.</p>'; });
  document.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
  document.querySelector('.lightbox-prev').addEventListener('click', () => openLightbox(currentImageIndex - 1));
  document.querySelector('.lightbox-next').addEventListener('click', () => openLightbox(currentImageIndex + 1));
  lightbox.addEventListener('click', (event) => { if (event.target === lightbox) closeLightbox(); });
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeLightbox(); if (event.key === 'ArrowLeft') openLightbox(currentImageIndex - 1); if (event.key === 'ArrowRight') openLightbox(currentImageIndex + 1); });
}

lucide.createIcons();
