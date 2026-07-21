// ============================================================
// SRI MANIKANTA POOJA STORES — HOME PAGE JS
// ============================================================

// Data comes from products.js

document.addEventListener('DOMContentLoaded', () => {
  initSite('index.html');
  initHeroSlider();
  renderCategories();
  renderFeaturedProducts();
  renderFestivals();
  renderTestimonials();
  renderGallery();
  renderStoreStatusBanner();
});


// ── Live Store Status in Header ───────────────────────────────
function renderStoreStatusBanner() {
  // Inject a small status pill into the features bar
  const featuresBar = document.querySelector('.features-bar-grid');
  if (!featuresBar) return;
  const status = getStoreStatus();
  const pill = document.createElement('div');
  pill.className = 'feature-item store-status-pill';
  pill.innerHTML = `
    <div class="feature-icon" style="background:${status.color}22;color:${status.color}">
      <i class="fas fa-${status.open ? 'door-open' : 'door-closed'}"></i>
    </div>
    <div class="feature-text">
      <strong style="color:${status.color}">${status.label}</strong>
      <small>${status.detail}</small>
    </div>`;
  // Insert before first child
  featuresBar.insertBefore(pill, featuresBar.firstChild);
  // Update grid to 5 columns
  featuresBar.style.gridTemplateColumns = 'repeat(5, 1fr)';
}


// ── Hero Slider ───────────────────────────────────────────────
const HERO_SLIDES = [
  {
    image: 'images/hero_slide_1.webp',
    tag: 'ॐ Welcome to Sri Manikanta Pooja Stores',
    title: 'Your Sacred <span class="highlight">Pooja Samagri</span><br>All in One Place',
    desc: 'Premium quality agarbatti, camphor, kumkum, haldi, diyas and all pooja essentials for every Hindu ritual and festival.',
    btn1: { text: 'Shop Now', href: 'shop.html', class: 'btn-primary btn-lg' },
    btn2: { text: 'View Categories', href: 'shop.html', class: 'btn-white btn-lg' },
    badge: { num: '500+', label: 'Products' }
  },
  {
    image: 'images/hero_slide_2.webp',
    tag: '✨ Festival Season Specials',
    title: 'Complete <span class="highlight">Festival Kits</span><br>For Every Occasion',
    desc: 'Diwali, Ganesh Chaturthi, Ugadi, Navratri — get everything you need in one box. Authentic, pure and delivered to your door.',
    btn1: { text: 'Festival Kits', href: 'shop.html?cat=festivals', class: 'btn-primary btn-lg' },
    btn2: { text: 'View All Items', href: 'shop.html', class: 'btn-white btn-lg' },
    badge: { num: '20+', label: 'Yrs in Business' }
  },
  {
    image: 'images/hero_slide_3.webp',
    tag: '💍 Wedding & Marriage Items',
    title: 'Complete <span class="highlight">Telugu Wedding</span><br>Samagri Available',
    desc: 'All traditional Telugu Brahmin wedding items — kankanam, mangalsutra thread, kalash, agnihotra samagri and more in one stop.',
    btn1: { text: 'Wedding Items', href: 'shop.html?cat=wedding', class: 'btn-primary btn-lg' },
    btn2: { text: 'View All Items', href: 'shop.html', class: 'btn-white btn-lg' },
    badge: { num: '25+', label: 'Yrs Experience' }
  },
  {
    image: 'images/hero_slide_4.webp',
    tag: '🕉️ Pure & Traditional',
    title: 'Beautiful <span class="highlight">God Idols</span><br>& Temple Brassware',
    desc: 'Enhance your mandir with our wide range of carefully crafted brass idols, pure silver diyas, and authentic pooja bells.',
    btn1: { text: 'Shop Idols', href: 'shop.html?cat=idols', class: 'btn-primary btn-lg' },
    btn2: { text: 'Visit Store', href: '#', class: 'btn-white btn-lg', onclick: 'openStoreMap(event)' },
    badge: { num: '100%', label: 'Authentic' }
  },
  {
    image: 'images/hero_slide_5.webp',
    tag: '🌿 Premium Fragrances',
    title: 'Pure <span class="highlight">Agarbatti & Dhoop</span><br>For Daily Pooja',
    desc: 'Fill your home with divine fragrance. Shop our premium collection of incense sticks, dhoop cones, and sambrani.',
    btn1: { text: 'View Products', href: 'shop.html?cat=agarbatti', class: 'btn-primary btn-lg' },
    btn2: { text: 'Contact Us', href: 'contact.html', class: 'btn-white btn-lg' },
    badge: { num: '5★', label: 'Quality' }
  },
  {
    image: 'images/hero_slide_6.webp',
    tag: '🔴 Sacred Offerings',
    title: 'Authentic <span class="highlight">Kumkum & Haldi</span><br>For Rituals',
    desc: '100% pure and natural turmeric powder, sindoor, and kumkum directly sourced for your daily spiritual needs.',
    btn1: { text: 'Shop Now', href: 'shop.html?cat=kumkum', class: 'btn-primary btn-lg' },
    btn2: { text: 'Shop Online', href: 'shop.html', class: 'btn-white btn-lg' },
    badge: { num: '1000+', label: 'Happy Clients' }
  },
  {
    image: 'images/hero_slide_7.webp',
    tag: '📿 Spiritual Accessories',
    title: 'Beautiful <span class="highlight">Malas & Garlands</span><br>For Deities',
    desc: 'Find the perfect adornments for your idols. We carry tulsi malas, rudraksha, and decorative garlands.',
    btn1: { text: 'View Malas', href: 'shop.html?cat=malas', class: 'btn-primary btn-lg' },
    btn2: { text: 'Shop Online', href: 'shop.html', class: 'btn-white btn-lg' },
    badge: { num: 'Best', label: 'Designs' }
  },
  {
    image: 'images/hero_slide_8.webp',
    tag: '🍽️ Pooja Essentials',
    title: 'Traditional <span class="highlight">Pooja Thalis</span><br>& Accessories',
    desc: 'Complete your pooja room with our beautiful brass and silver-plated thalis, plates, and aarti items.',
    btn1: { text: 'Shop Thalis', href: 'shop.html?cat=thali', class: 'btn-primary btn-lg' },
    btn2: { text: 'Shop Online', href: 'shop.html', class: 'btn-white btn-lg' },
    badge: { num: 'Pure', label: 'Brass' }
  },
  {
    image: 'images/hero_slide_9.webp',
    tag: '🪔 Light the Lamp',
    title: 'Pure <span class="highlight">Pooja Oils & Ghee</span><br>For Deepam',
    desc: 'Ensure a long-lasting and pure flame. Shop our premium sesame oil, castor oil, and pure cow ghee for diyas.',
    btn1: { text: 'Shop Oils', href: 'shop.html?cat=oils', class: 'btn-primary btn-lg' },
    btn2: { text: 'Shop Online', href: 'shop.html', class: 'btn-white btn-lg' },
    badge: { num: '100%', label: 'Pure' }
  }
];

let currentSlide = 0;
let sliderTimer = null;
let isPaused = false;
const SLIDE_DURATION = 2000; // 2 seconds fixed

function randDuration() {
  return SLIDE_DURATION;
}

function initHeroSlider() {
  const hero = document.getElementById('hero-slider');
  if (!hero) return;

  const existingSlide0 = document.getElementById('slide-0');
  let newHtml = '';
  
  HERO_SLIDES.forEach((s, i) => {
    // Skip injecting slide-0 if it's already pre-rendered in HTML
    if (i === 0 && existingSlide0) return;
    
    newHtml += `
    <div class="hero-slide${i === 0 ? ' active' : ''}" id="slide-${i}">
      <picture>
        <source media="(max-width: 768px)" srcset="${s.image.replace('.webp', '_mobile.webp')}">
        <img src="${s.image}" alt="${s.tag}" loading="${i === 0 ? 'eager' : 'lazy'}" fetchpriority="${i === 0 ? 'high' : 'auto'}" width="1920" height="800" decoding="async" onerror="this.style.display='none'">
      </picture>
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div class="container">
          <div class="hero-text">
            <div class="hero-tag"><i class="fas fa-om"></i> ${s.tag}</div>
            <h1 class="hero-title">${s.title}</h1>
            <p class="hero-desc">${s.desc}</p>
            <div class="hero-actions">
              <a href="${s.btn1.href}" class="btn ${s.btn1.class}">${s.btn1.text} <i class="fas fa-arrow-right"></i></a>
              <a href="${s.btn2.href}" class="btn ${s.btn2.class}">${s.btn2.text}</a>
            </div>
          </div>
        </div>
      </div>
      <div class="hero-badge">
        <div class="hero-badge-num">${s.badge.num}</div>
        <div class="hero-badge-label">${s.badge.label}</div>
      </div>
    </div>
  `;
  });

  if (newHtml) {
    if (existingSlide0) {
        hero.insertAdjacentHTML('beforeend', newHtml);
    } else {
        hero.innerHTML = newHtml;
    }
  }

  hero.insertAdjacentHTML('beforeend', `<div class="hero-progress-bar" id="hero-progress"></div>`);



  const dotsContainer = document.getElementById('hero-dots');
  if (dotsContainer) {
    dotsContainer.innerHTML = HERO_SLIDES.map((_, i) =>
      `<button class="hero-dot${i === 0 ? ' active' : ''}" data-slide="${i}" aria-label="Slide ${i + 1}"></button>`
    ).join('');
    dotsContainer.addEventListener('click', e => {
      const btn = e.target.closest('[data-slide]');
      if (btn) goToSlide(+btn.dataset.slide);
    });
  }

  document.getElementById('hero-prev')?.addEventListener('click', () => goToSlide((currentSlide - 1 + HERO_SLIDES.length) % HERO_SLIDES.length));
  document.getElementById('hero-next')?.addEventListener('click', () => goToSlide((currentSlide + 1) % HERO_SLIDES.length));

  startAutoSlide();

  hero.addEventListener('mouseenter', () => {
    isPaused = true;
    clearTimeout(sliderTimer);
    const bar = document.getElementById('hero-progress');
    if (bar) {
      const computed = getComputedStyle(bar).width;
      const heroW = hero.offsetWidth;
      bar.style.transition = 'none';
      bar.style.width = computed;
      bar.classList.remove('animating');
    }
  });
  hero.addEventListener('mouseleave', () => {
    isPaused = false;
    startAutoSlide();
  });
}

function goToSlide(n) {
  document.querySelectorAll('.hero-slide').forEach((s, i) => s.classList.toggle('active', i === n));
  document.querySelectorAll('.hero-dot').forEach((d, i) => d.classList.toggle('active', i === n));
  currentSlide = n;
  if (!isPaused) startAutoSlide();
}

function startAutoSlide() {
  clearTimeout(sliderTimer);
  const duration = randDuration();
  const bar = document.getElementById('hero-progress');
  if (bar) {
    bar.style.transition = 'none';
    bar.style.width = '0%';
    void bar.offsetWidth;
    bar.style.transition = `width ${duration}ms linear`;
    bar.style.width = '100%';
  }
  sliderTimer = setTimeout(() => {
    if (!isPaused) goToSlide((currentSlide + 1) % HERO_SLIDES.length);
  }, duration);
}

// ── Categories ────────────────────────────────────────────────
function renderCategories() {
  const grid = document.getElementById('categories-grid');
  if (!grid) return;
  grid.innerHTML = CATEGORIES.map((c, i) => `
    <a href="shop.html?cat=${c.id}" class="category-card-sm reveal reveal-delay-${(i % 4) + 1}" id="cat-${c.id}">
      <img src="${c.img}" alt="${c.name}" class="cat-sm-img" loading="lazy" width="300" height="300" decoding="async">
      <div class="cat-sm-name">${c.name}</div>
      <div class="cat-sm-count">${c.count} items</div>
    </a>
  `).join('');
}

// ── Featured Products ─────────────────────────────────────────
function renderFeaturedProducts() {
  const grid = document.getElementById('featured-products');
  if (!grid || typeof PRODUCTS === 'undefined') return;
  // Sort by reviews to get the most "popular" or "best selling" items
  const bestSelling = [...PRODUCTS].sort((a, b) => b.reviews - a.reviews).slice(0, 8);
  grid.innerHTML = bestSelling.map(p => renderProductCard(p)).join('');
}

// ── Festivals ─────────────────────────────────────────────────
function renderFestivals() {
  const grid = document.getElementById('festivals-grid');
  if (!grid) return;
  grid.innerHTML = FESTIVALS.map((f, i) => `
    <a href="shop.html?cat=${f.cat}" class="festival-card reveal reveal-delay-${(i % 3) + 1}">
      <div class="festival-img" style="background: url('${f.image}') center/cover no-repeat;">
        <div class="festival-overlay"></div>
      </div>
      <div class="festival-body">
        <div class="festival-name">${f.name}</div>
        <div class="festival-date" style="font-family:var(--font-telugu)">${f.telugu}</div>
        <div class="festival-date"><i class="fas fa-calendar-alt" style="color:var(--saffron);margin-right:4px"></i>${f.date}</div>
        <div class="festival-tag">Shop Festival Items →</div>
      </div>
    </a>
  `).join('');
}

// ── Testimonials ──────────────────────────────────────────────
const TESTIMONIALS = [
  {
    text: "The quality of pooja samagri at Sri Manikanta is truly exceptional. We source all our temple requirements from here because of their uncompromising purity and prompt service.",
    name: 'Raghavendra Shastri', location: 'Head Priest, Local Temple', avatar: '🕉️', rating: 5
  },
  {
    text: "I am deeply impressed by their extensive collection and authentic products. Their wedding kits are incredibly comprehensive and thoughtfully curated for traditional Telugu rituals.",
    name: 'Suryanarayana Murthy', location: 'Dilsukhnagar, Hyderabad', avatar: '🙏', rating: 5
  },
  {
    text: "As a regular customer for the past 5 years, I can vouch for their consistency. From fresh camphor to premium agarbattis, every item reflects devotion and professional quality.",
    name: 'Meenakshi Iyer', location: 'LB Nagar, Hyderabad', avatar: '🌸', rating: 5
  },
  {
    text: "Outstanding customer service and fast delivery! They helped me find the exact brass idols I needed for my new home. Highly recommended for all spiritual needs.",
    name: 'Venkatesh Rao', location: 'Kothapet, Hyderabad', avatar: '🪔', rating: 5
  },
  {
    text: "Very genuine products and great packaging. The ghee quality is excellent for daily deeparadhana. Highly recommended store.",
    name: 'Anil Kumar', location: 'Kukatpally, Hyderabad', avatar: '🔔', rating: 5
  },
  {
    text: "I found everything I needed for Varamahalakshmi Vratham here. The prices are very reasonable and the staff is very helpful.",
    name: 'Lakshmi Narayana', location: 'Secunderabad', avatar: '🌿', rating: 5
  }
];

function renderTestimonials() {
  const grid = document.getElementById('testimonials-grid');
  if (!grid) return;
  grid.innerHTML = TESTIMONIALS.map(t => `
    <div class="testimonial-card reveal">
      <div class="review-stars">${'★'.repeat(t.rating)}${'☆'.repeat(5 - t.rating)}</div>
      <p class="testimonial-text">"${t.text}"</p>
      <div class="testimonial-author">
        <div class="author-avatar">${t.avatar}</div>
        <div>
          <div class="author-name">${t.name}</div>
          <div class="author-loc"><i class="fas fa-map-marker-alt" style="color:var(--saffron);font-size:10px"></i> ${t.location}</div>
        </div>
      </div>
    </div>
  `).join('');
}

// ── Gallery — Real store photos from Google Maps ─────────────
function renderGallery() {
  const grid = document.getElementById('gallery-grid');
  if (!grid) return;
  const gImages = [
    { src: 'images/store_exterior_1.webp', alt: 'Sri Manikanta Pooja Store Front' },
    { src: 'images/store_1.webp', alt: 'Sri Manikanta Pooja Store — View 1' },
    { src: 'images/store_2.webp', alt: 'Sri Manikanta Store — View 2' },
    { src: 'images/store_3.webp', alt: 'Sri Manikanta Store — View 3' },
    { src: 'images/store_4.webp', alt: 'Sri Manikanta Store — View 4' },
    { src: 'images/store_5.webp', alt: 'Sri Manikanta Store — View 5' },
    { src: 'images/store_6.webp', alt: 'Sri Manikanta Store — View 6' },
    { src: 'images/store_7.webp', alt: 'Sri Manikanta Store — View 7' },
    { src: 'images/store_8.webp', alt: 'Sri Manikanta Store — View 8' }
  ];
  grid.innerHTML = gImages.map((img, i) => `
    <div class="gallery-item reveal reveal-delay-${(i % 3) + 1}" onclick="openGalleryModal('${img.src}','${img.alt}')">
      <img src="${img.src}" alt="${img.alt}" loading="lazy" onerror="this.parentElement.style.display='none'">
      <div class="gallery-overlay"><i class="fas fa-expand-arrows-alt"></i></div>
    </div>
  `).join('');
}

function openGalleryModal(src, alt) {
  let m = document.getElementById('gallery-modal');
  if (!m) {
    m = document.createElement('div');
    m.id = 'gallery-modal';
    m.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;cursor:zoom-out';
    m.innerHTML = '<img id="gallery-modal-img" style="max-width:90vw;max-height:90vh;border-radius:12px;object-fit:contain"><button onclick="document.getElementById(\'gallery-modal\').remove()" style="position:absolute;top:20px;right:20px;width:44px;height:44px;border-radius:50%;background:white;border:none;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="fas fa-times"></i></button>';
    m.addEventListener('click', e => { if (e.target === m) m.remove(); });
    document.body.appendChild(m);
  }
  document.getElementById('gallery-modal-img').src = src;
  document.getElementById('gallery-modal-img').alt = alt;
}


