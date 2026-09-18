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

const galleryFilters = [...document.querySelectorAll('.gallery-filter')];
const galleryCards = [...document.querySelectorAll('.gallery-card')];

galleryFilters.forEach((filterButton) => {
  filterButton.addEventListener('click', () => {
    const selectedLevel = filterButton.dataset.filter;
    galleryFilters.forEach((button) => button.classList.toggle('active', button === filterButton));
    galleryCards.forEach((card) => {
      const shouldShow = selectedLevel === 'todos' || card.dataset.level === selectedLevel;
      card.classList.toggle('is-hidden', !shouldShow);
    });
  });
});

lucide.createIcons();
