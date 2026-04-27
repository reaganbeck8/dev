// ── Footer year ──────────────────────────────────────────────
document.querySelectorAll('#year').forEach(el => {
  el.textContent = new Date().getFullYear();
});

// ── Nav scroll behaviour ──────────────────────────────────────
const nav = document.getElementById('nav');
if (nav) {
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
  });
}

// ── Mobile menu ───────────────────────────────────────────────
const mobileMenu = document.getElementById('mobile-menu');
const menuBtn    = document.getElementById('menu-btn');
const menuClose  = document.getElementById('menu-close');

if (menuBtn && mobileMenu) {
  menuBtn.addEventListener('click', () => mobileMenu.classList.add('open'));
}
if (menuClose && mobileMenu) {
  menuClose.addEventListener('click', () => mobileMenu.classList.remove('open'));
}
document.querySelectorAll('.mobile-link').forEach(link => {
  link.addEventListener('click', () => mobileMenu && mobileMenu.classList.remove('open'));
});

// ── Hero video/slide rotation ─────────────────────────────────
const slides = document.querySelectorAll('.hero-slide');
const dots   = document.querySelectorAll('.dot');
let current  = 0;
const INTERVAL = 8000;

function goTo(index) {
  if (!slides.length) return;
  slides[current].classList.remove('active');
  if (dots[current]) dots[current].classList.remove('active');
  current = index % slides.length;
  slides[current].classList.add('active');
  if (dots[current]) dots[current].classList.add('active');

  if (slides[current].tagName === 'VIDEO') {
    slides[current].currentTime = 0;
    slides[current].play().catch(() => {});
  }
}

dots.forEach(dot => {
  dot.addEventListener('click', () => {
    clearInterval(timer);
    goTo(parseInt(dot.dataset.target));
    timer = setInterval(() => goTo(current + 1), INTERVAL);
  });
});

document.querySelectorAll('.hero-slide video, video.hero-slide').forEach(v => v.load());

let timer = setInterval(() => goTo(current + 1), INTERVAL);

// ── Fade-in on scroll ─────────────────────────────────────────
const fadeEls = document.querySelectorAll('.fade-in');
if (fadeEls.length && 'IntersectionObserver' in window) {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  fadeEls.forEach(el => observer.observe(el));
}

// ── YouTube facade: lazy-load + autoplay muted on scroll ──────
const ytFacades = document.querySelectorAll('.yt-facade[data-yt]');

function activateYt(facade) {
  const id = facade.dataset.yt;
  if (!id || facade.classList.contains('playing')) return;
  const iframe = document.createElement('iframe');
  iframe.src = 'https://www.youtube-nocookie.com/embed/' + id
    + '?autoplay=1&mute=1&loop=1&playlist=' + id
    + '&rel=0&modestbranding=1&playsinline=1';
  iframe.allow = 'autoplay; encrypted-media; picture-in-picture';
  iframe.allowFullscreen = true;
  iframe.title = facade.querySelector('.yt-thumb') ? facade.querySelector('.yt-thumb').alt : 'Video';
  facade.classList.add('playing');
  facade.appendChild(iframe);
}

if (ytFacades.length && 'IntersectionObserver' in window) {
  const ytObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        activateYt(entry.target);
        ytObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });
  ytFacades.forEach(f => ytObserver.observe(f));
}

ytFacades.forEach(f => f.addEventListener('click', () => activateYt(f)));
