// ============================================================
// SRI MANIKANTA POOJA STORES — MAIN.JS
// Global: Header, Footer, Cart, Floating Buttons, Utilities
// ============================================================

const STORE = {
  name:      'Sri Manikanta Pooja Stores',
  phone1:    '+919110582086',
  phone2:    '+919849985423',
  phone3:    '+919182814818',
  whatsapp:  '919849985423',
  whatsapp2: '919110582086',
  whatsappGroup: 'https://chat.whatsapp.com/KoJUjAeLjgFDm00ZMESWnx',
  instagram: 'https://www.instagram.com/srimanikanta_poojastores?igsh=MWNqYmdxeGl1Nm9qdw==',
  address:   'Sri Manikanta pooja stores, P&T coloney, dilsukhnagar., Konark Theatre Ln, opposite Sanjay Super Market, Dilsukhnagar, Hyderabad, Telangana 500060',
  mapEmbed:  'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7615.796686244467!2d78.51863469196398!3d17.36862398406134!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb9992e24cfac1%3A0x84d6068085b897e0!2sSri%20manikanta%20pooja%20stores!5e0!3m2!1sen!2sin!4v1782628485698!5m2!1sen!2sin',
  mapLink:   'https://maps.app.goo.gl/y4TFYSYQsG3ZiySJ8',
  hours: {
    weekdays: '8:00 AM – 10:00 PM',
    saturday: '8:00 AM – 10:00 PM',
    sunday:   '8:30 AM – 10:00 PM',
    festivals:'7:00 AM – 11:30 PM'
  }
};

// ── Cart ─────────────────────────────────────────────────────
const Cart = {
  items: [],
  load() {
    try { this.items = JSON.parse(localStorage.getItem('smps_cart') || '[]'); }
    catch { this.items = []; }
  },
  save()        { localStorage.setItem('smps_cart', JSON.stringify(this.items)); },
  count()       { return this.items.reduce((s, i) => s + i.qty, 0); },
  total()       { return this.items.reduce((s, i) => s + i.price * i.qty, 0); },
  add(product, qty = 1, variant = '') {
    const key  = `${product.id}_${variant}`;
    const existing = this.items.find(i => i.key === key);
    if (existing) { existing.qty += qty; }
    else {
      this.items.push({
        key, id: product.id, name: product.name,
        price: product.price, image: product.image,
        variant, qty
      });
    }
    this.save();
    updateCartBadge();
    showToast(`✅ ${product.name} added to cart!`, 'success');
  },
  remove(key) {
    this.items = this.items.filter(i => i.key !== key);
    this.save();
    updateCartBadge();
  },
  updateQty(key, qty) {
    const item = this.items.find(i => i.key === key);
    if (item) { item.qty = Math.max(1, qty); this.save(); }
  },
  clear() { this.items = []; this.save(); updateCartBadge(); }
};

// ── Wishlist ──────────────────────────────────────────────────
const Wishlist = {
  items: [],
  load() {
    try { this.items = JSON.parse(localStorage.getItem('smps_wishlist') || '[]'); }
    catch { this.items = []; }
  },
  save() { localStorage.setItem('smps_wishlist', JSON.stringify(this.items)); },
  toggle(id) {
    id = String(id);
    const idx = this.items.indexOf(id);
    if (idx > -1) {
      this.items.splice(idx, 1);
      showToast('Removed from Wishlist', 'default', 2000);
    } else {
      this.items.push(id);
      showToast('❤️ Added to Wishlist!', 'success', 2000);
    }
    this.save();
    return this.items.includes(id);
  },
  includes(id) { return this.items.includes(String(id)); }
};

// ── Store Status (Open/Closed) ────────────────────────────────
function getStoreStatus() {
  const now = new Date();
  const day  = now.getDay(); // 0=Sun, 1-6=Mon-Sat
  const h    = now.getHours();
  const m    = now.getMinutes();
  const time = h * 60 + m;

  // Hours in minutes past midnight
  const isSunday   = day === 0;
  const openTime   = isSunday ? 8 * 60 + 30 : 8 * 60;  // 8:30 Sun, 8:00 others
  const closeTime  = 22 * 60;  // 10:00 PM all days
  const closingSoon = closeTime - 60; // 1hr before closing

  if (time >= openTime && time < closeTime) {
    if (time >= closingSoon) {
      return { open: true, soon: true, label: '⚠️ Closing Soon', detail: 'Closes at 10:00 PM', color: '#FFA000' };
    }
    return { open: true, soon: false, label: '✅ Open Now', detail: isSunday ? 'Closes at 10:00 PM' : 'Closes at 10:00 PM', color: '#2E7D32' };
  }
  if (time < openTime) {
    const minsUntilOpen = openTime - time;
    const h2 = Math.floor(minsUntilOpen / 60), m2 = minsUntilOpen % 60;
    return { open: false, soon: false, label: '🔴 Currently Closed', detail: `Opens in ${h2 > 0 ? h2 + 'h ' : ''}${m2}m`, color: '#C62828' };
  }
  return { open: false, soon: false, label: '🔴 Currently Closed', detail: isSunday ? 'Opens 8:30 AM tomorrow' : 'Opens 8:00 AM tomorrow', color: '#C62828' };
}

// ── Toast ─────────────────────────────────────────────────────
function showToast(msg, type = 'default', duration = 3000) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = msg;
  container.appendChild(toast);
  setTimeout(() => {
    toast.classList.add('fade-out');
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// ── Cart Badge ────────────────────────────────────────────────
function updateCartBadge() {
  const badges = document.querySelectorAll('.cart-count');
  const count  = Cart.count();
  badges.forEach(b => {
    b.textContent = count;
    b.style.display = count > 0 ? 'flex' : 'none';
  });
  // Update floating cart button count
  const floatingCount = document.getElementById('floating-cart-count');
  if (floatingCount) {
    floatingCount.textContent = count;
    floatingCount.style.display = count > 0 ? 'flex' : 'none';
  }
}

// ── Scroll Reveal ─────────────────────────────────────────────
function initScrollReveal() {
  const els = document.querySelectorAll('.reveal');
  const io  = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  els.forEach(el => io.observe(el));
}

// ── Header Scroll ─────────────────────────────────────────────
function initHeaderScroll() {
  const header = document.getElementById('site-header');
  if (!header) return;
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 50);
  }, { passive: true });
}

// ── Header HTML ───────────────────────────────────────────────
function renderHeader(activePage = '') {
  const navLinks = [
    { href: 'index.html',    label: 'Home',        icon: 'fa-home' },
    { href: 'shop.html',     label: 'Shop',        icon: 'fa-store' },
    { href: '#',             label: 'Visit Store', icon: 'fa-map-marker-alt', onclick: 'openStoreMap(event)' },
    { href: 'reviews.html',  label: 'Review Us',   icon: 'fa-star' },
    { href: 'about.html',   label: 'About Us',    icon: 'fa-info-circle' },
    { href: 'contact.html', label: 'Contact Us',     icon: 'fa-phone' }
  ];
  const tickerItems = [
    '📞 Call us: +91 91105 82086 | +91 98499 85423 | +91 91828 14818',
    '🚚 Home Delivery — Free above ₹5000 or within 5km',
    '💬 WhatsApp us: +91 91105 82086 | +91 90147 58196',
    '🌿 100% Natural & Pure Ingredients',
    '🛒 Wholesale items available at best prices',
    '⭐ Best Quality Guaranteed on all products',
    '💰 Affordable prices — Unbeatable value',
    '🎁 Special festival offers available in store',
  ];
  const ticker = [...tickerItems, ...tickerItems].map(t => `<span class="ticker-item"><i class="fas fa-om"></i> ${t}</span>`).join('');

  return `
  <div class="ticker-bar">
    <div class="ticker-inner">${ticker}</div>
  </div>
  <nav>
    <div class="header-inner" style="display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 20px; width: 100%; padding: 0 40px;">
      
      <!-- Left: Menu + Logo -->
      <div class="header-left" style="display: flex; align-items: center; gap: 24px;">
        <!-- ☰ All Drawer Trigger -->
        <button class="all-menu-btn" id="all-menu-btn" aria-label="Open Menu" aria-expanded="false">
          <span class="hamburger-lines"><span></span><span></span><span></span></span>
          <span class="all-label">All</span>
        </button>
        <a href="index.html" class="header-logo">
          <div class="logo-icon">🕉️</div>
          <div class="logo-text">
            <span class="logo-name">Sri Manikanta</span>
            <span class="logo-sub" style="color: var(--gold-light); font-weight: 600;">Pooja Stores</span>
            <span class="logo-sub" style="margin-top:2px;">P&T Colony, Dilsukhnagar, Hyderabad</span>
          </div>
        </a>
      </div>

      <!-- Center: Nav Links -->
      <div class="header-nav" style="display: flex; justify-content: center; gap: 4px;">
        <a href="index.html" class="nav-link${activePage === 'index.html' || activePage === '' ? ' active' : ''}">Home</a>
        <a href="shop.html" class="nav-link${activePage === 'shop.html' ? ' active' : ''}">Shop</a>
        <a href="#" class="nav-link" onclick="openStoreMap(event)">Visit Store</a>
        
        <!-- Categories Dropdown -->
        <div class="nav-dropdown">
          <a href="shop.html" class="nav-link">Categories <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i></a>
          <div class="dropdown-menu">
            ${typeof CATEGORIES !== 'undefined' ? CATEGORIES.map(c => `<a href="shop.html?cat=${c.id}" class="dropdown-item">${c.emoji || '<i class="fas fa-angle-right"></i>'} ${c.name}</a>`).join('') : '<a href="shop.html" class="dropdown-item">All Categories</a>'}
          </div>
        </div>

        <a href="about.html" class="nav-link${activePage === 'about.html' ? ' active' : ''}">About Us</a>
        <a href="reviews.html" class="nav-link${activePage === 'reviews.html' ? ' active' : ''}" style="color: #FFD700; font-weight: 700;"><i class='fas fa-star' style='font-size:11px;margin-right:4px'></i>Review Us</a>
        <a href="contact.html" class="nav-link${activePage === 'contact.html' ? ' active' : ''}">Contact Us</a>
      </div>

      <!-- Right: Actions -->
      <div class="header-actions" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
        <!-- Quick Enquiry Button -->
        <a href="#" class="header-enquiry-btn" onclick="openEnquiry(event)" title="Quick Enquiry on WhatsApp">
          <i class="fab fa-whatsapp"></i>
          <span>Quick Enquiry</span>
        </a>
        <div class="header-search-wrap" id="search-wrap">
          <button class="header-search-btn" id="search-toggle" title="Search"><i class="fas fa-search"></i></button>
          <input type="text" class="header-search-input" id="header-search" placeholder="Search products…" aria-label="Search">
        </div>
        
        <a href="cart.html" class="cart-btn" title="Cart" aria-label="Cart">
          <i class="fas fa-shopping-bag"></i>
          <span class="cart-count" style="display:none">0</span>
        </a>

        <!-- Profile Dropdown -->
        <div class="nav-dropdown" id="header-profile-dropdown" style="display: flex; align-items: center;">
          <a href="#" class="cart-btn" style="border: none; background: transparent; color: rgba(255,255,255,0.85);" title="Account" aria-label="Account">
            <i class="fas fa-user-circle" style="font-size: 20px;"></i>
          </a>
          <div class="dropdown-menu" style="left: auto; right: 0; transform: translateY(10px);">
            <div style="padding: 10px 16px; border-bottom: 1px solid var(--border-light); font-weight: 600; font-size: 14px; color: var(--maroon);" id="header-user-name">Hello, Guest</div>
            <a href="dashboard/profile.html" class="dropdown-item" id="header-profile-link" style="display:none;"><i class="fas fa-id-card"></i> My Profile</a>
            <a href="dashboard/orders.html" class="dropdown-item" id="header-orders-link" style="display:none;"><i class="fas fa-box"></i> My Orders</a>
            <a href="login.html" class="dropdown-item" id="header-login-link"><i class="fas fa-sign-in-alt"></i> Sign In</a>
            <a href="#" class="dropdown-item" id="header-logout-link" onclick="handleSwitchAccount(event)" style="display:none;"><i class="fas fa-sign-out-alt"></i> Switch Account / Logout</a>
          </div>
        </div>
        <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
    <div class="mobile-nav" id="mobile-nav">
      ${navLinks.map(l => {
        const onclickAttr = l.onclick ? ` onclick="${l.onclick}"` : '';
        return `<a href="${l.href}"${onclickAttr}><i class="fas ${l.icon}"></i>${l.label}</a>`;
      }).join('')}
      <a href="cart.html"><i class="fas fa-shopping-bag"></i>Cart <span class="cart-count" style="display:none">0</span></a>
    </div>
  </nav>

  <!-- ── Amazon-Style Side Drawer ─────────────────────────── -->
  <div class="side-drawer-backdrop" id="drawer-backdrop" onclick="closeDrawer()"></div>
  <div class="side-drawer" id="side-drawer" role="navigation" aria-label="Side Navigation">
    <!-- Drawer Header -->
    <div class="drawer-header">
      <div class="drawer-user" id="drawer-user-info">
        <div class="drawer-avatar"><i class="fas fa-user-circle"></i></div>
        <div>
          <div class="drawer-hello">Hello, Guest</div>
          <a href="login.html" class="drawer-signin-link">Sign In &rsaquo;</a>
        </div>
      </div>
      <button class="drawer-close" onclick="closeDrawer()" aria-label="Close Menu">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Shop By Category -->
    <div class="drawer-section">
      <div class="drawer-section-title"><i class="fas fa-th-large"></i> Shop By Category</div>
      <a href="shop.html?cat=all" class="drawer-link"><i class="fas fa-store"></i> All Products</a>
      <a href="shop.html?cat=pooja-items" class="drawer-link"><i class="fas fa-pray"></i> Pooja Items</a>
      <a href="shop.html?cat=agarbatti" class="drawer-link"><i class="fas fa-fire"></i> Agarbatti &amp; Incense</a>
      <a href="shop.html?cat=camphor" class="drawer-link"><i class="fas fa-fire-alt"></i> Camphor (Kapoor)</a>
      <a href="shop.html?cat=flowers" class="drawer-link"><i class="fas fa-seedling"></i> Flowers &amp; Garlands</a>
      <a href="shop.html?cat=panchapatra" class="drawer-link"><i class="fas fa-tint"></i> Panchapatra &amp; Utensils</a>
      <a href="shop.html?cat=brass" class="drawer-link"><i class="fas fa-ring"></i> Brass Items</a>
      <a href="shop.html?cat=copper" class="drawer-link"><i class="fas fa-coins"></i> Copper Items</a>
      <a href="shop.html?cat=festivals" class="drawer-link"><i class="fas fa-star"></i> Festival Products</a>
    </div>

    <!-- My Account -->
    <div class="drawer-section">
      <div class="drawer-section-title"><i class="fas fa-user"></i> My Account</div>
      <a href="dashboard/profile.html" class="drawer-link"><i class="fas fa-id-card"></i> Profile</a>
      <a href="dashboard/orders.html" class="drawer-link"><i class="fas fa-box"></i> My Orders</a>
      <a href="dashboard/wishlist.html" class="drawer-link"><i class="fas fa-heart"></i> Wishlist</a>
    </div>

    <!-- Help & Settings -->
    <div class="drawer-section">
      <div class="drawer-section-title"><i class="fas fa-headset"></i> Help &amp; Settings</div>
      <a href="contact.html" class="drawer-link"><i class="fas fa-phone-alt"></i> Contact Us</a>
      <a href="#" class="drawer-link" onclick="openEnquiry(event);closeDrawer();"><i class="fab fa-whatsapp" style="color:#25D366"></i> Quick Enquiry</a>
      <a href="#faqs" class="drawer-link"><i class="fas fa-question-circle"></i> FAQs</a>
      <a href="#returns" class="drawer-link"><i class="fas fa-undo"></i> Returns Policy</a>
      <a href="#privacy" class="drawer-link"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
    </div>

    <!-- Bottom Actions -->
    <div class="drawer-bottom">
      <a href="cart.html" class="drawer-bottom-btn">
        <i class="fas fa-shopping-bag"></i>
        <span>Cart</span>
        <span class="cart-count drawer-cart-count" style="display:none">0</span>
      </a>
      <button class="drawer-bottom-btn" id="drawer-logout-btn" onclick="handleDrawerLogout()" style="display:none">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </button>
      <a href="login.html" class="drawer-bottom-btn" id="drawer-login-btn">
        <i class="fas fa-sign-in-alt"></i>
        <span>Login</span>
      </a>
    </div>
  </div>

  <!-- ── Quick Enquiry Modal ─────────────────────────── -->
  <div class="enquiry-backdrop" id="enquiry-modal" onclick="closeEnquiryBackdrop(event)">
    <div class="enquiry-box">

      <!-- Header -->
      <div class="enquiry-header">
        <div class="enquiry-header-title">
          <span class="enquiry-wa-icon"><i class="fab fa-whatsapp"></i></span>
          <div>
            <h3>Quick Enquiry</h3>
            <p>Tell us what you need — we'll reply in minutes!</p>
          </div>
        </div>
        <button class="enquiry-close-btn" onclick="closeEnquiry()" aria-label="Close">&times;</button>
      </div>

      <!-- Two-column body -->
      <div class="enquiry-layout">

        <!-- LEFT: Store Info Panel -->
        <div class="enquiry-info-panel">
          <div class="enquiry-store-name">🕉️ Sri Manikanta<br><span>Pooja Stores</span></div>
          <div class="enquiry-info-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>P&T Colony, Dilsukhnagar,<br>Hyderabad – 500060</span>
          </div>
          <div class="enquiry-info-item">
            <i class="fas fa-clock"></i>
            <span>Open Daily<br><strong>8:00 AM – 10:00 PM</strong></span>
          </div>
          <div class="enquiry-info-item">
            <i class="fab fa-whatsapp" style="color:#25D366"></i>
            <span>Avg. reply time<br><strong>Under 30 mins</strong></span>
          </div>
          <a href="tel:+919110582086" class="enquiry-call-btn">
            <i class="fas fa-phone-alt"></i> Call Now
          </a>
          <a href="https://wa.me/919110582086" target="_blank" class="enquiry-wa-direct-btn">
            <i class="fab fa-whatsapp"></i> WhatsApp Direct
          </a>
          <div class="enquiry-info-badges">
            <span>✅ Bulk Orders</span>
            <span>🏠 Home Delivery</span>
            <span>💍 Wedding Kits</span>
            <span>🪔 Festival Items</span>
          </div>
        </div>

        <!-- RIGHT: Form -->
        <div class="enquiry-body">
          <div class="enquiry-row">
            <div class="enquiry-field">
              <label>Your Name <span>*</span></label>
              <input type="text" id="enq-name" placeholder="e.g. Rajesh Kumar" autocomplete="name">
            </div>
            <div class="enquiry-field">
              <label>Mobile Number</label>
              <input type="tel" id="enq-phone" placeholder="+91 XXXXX XXXXX" autocomplete="tel">
            </div>
          </div>
          <div class="enquiry-field">
            <label>Subject</label>
            <select id="enq-subject">
              <option value="Product Enquiry">🛍️ Product Enquiry</option>
              <option value="Bulk / Wholesale Order">📦 Bulk / Wholesale Order</option>
              <option value="Wedding Samagri">💍 Wedding Samagri</option>
              <option value="Festival Kit">🪔 Festival Kit</option>
              <option value="Price Check">💰 Price Check</option>
              <option value="Home Delivery">🚚 Home Delivery</option>
              <option value="Custom Request">✨ Custom Request</option>
              <option value="Other">💬 Other</option>
            </select>
          </div>
          <div class="enquiry-field">
            <label>Your Message <span>*</span></label>
            <textarea id="enq-message" rows="4" placeholder="Tell us what you need — product name, quantity, occasion, etc."></textarea>
          </div>
          <div id="enq-error" class="enquiry-error" style="display:none"></div>
          <button class="enquiry-send-btn" onclick="sendEnquiry()">
            <i class="fab fa-whatsapp"></i> Send via WhatsApp
          </button>
          <p class="enquiry-note"><i class="fas fa-lock"></i> Your info is only shared with our store via WhatsApp</p>
        </div>

      </div>
    </div>
  </div>`;
}

function openStoreMap(e) {
  e.preventDefault();
  const modal = document.getElementById('store-map-modal');
  if (modal) { modal.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeStoreMap(e) {
  if (e.target.id === 'store-map-modal') closeStoreMapBtn();
}
function closeStoreMapBtn() {
  const modal = document.getElementById('store-map-modal');
  if (modal) { modal.classList.remove('open'); document.body.style.overflow = ''; }
}

// ── Quick Enquiry Modal ───────────────────────────────────────
function openEnquiry(e) {
  if (e) e.preventDefault();
  const modal = document.getElementById('enquiry-modal');
  if (modal) { modal.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeEnquiry() {
  const modal = document.getElementById('enquiry-modal');
  if (modal) { modal.classList.remove('open'); document.body.style.overflow = ''; }
}
function closeEnquiryBackdrop(e) {
  if (e.target.id === 'enquiry-modal') closeEnquiry();
}
function sendEnquiry() {
  const name    = (document.getElementById('enq-name')?.value || '').trim();
  const phone   = (document.getElementById('enq-phone')?.value || '').trim();
  const subject = document.getElementById('enq-subject')?.value || 'Product Enquiry';
  const message = (document.getElementById('enq-message')?.value || '').trim();
  const errEl   = document.getElementById('enq-error');
  if (!name || !message) {
    errEl.textContent = '⚠️ Please enter your name and message.';
    errEl.style.display = 'block';
    return;
  }
  errEl.style.display = 'none';
  const waMsg = `🕉️ *New Enquiry – Sri Manikanta Pooja Stores*%0A%0A` +
    `👤 *Name:* ${encodeURIComponent(name)}%0A` +
    (phone ? `📞 *Mobile:* ${encodeURIComponent(phone)}%0A` : '') +
    `📋 *Subject:* ${encodeURIComponent(subject)}%0A` +
    `💬 *Message:* ${encodeURIComponent(message)}`;
  window.open(`https://wa.me/${STORE.whatsapp2}?text=${waMsg}`, '_blank');
  closeEnquiry();
  // Reset form
  ['enq-name','enq-phone','enq-message'].forEach(id => { const el = document.getElementById(id); if(el) el.value=''; });
}

// ── Footer HTML ───────────────────────────────────────────────
function renderFooter() {
  return `
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <span class="logo-name">🕉️ Sri Manikanta Pooja Stores</span>
        <p class="footer-tagline">
          Your trusted source for authentic Hindu pooja samagri in Hyderabad since 2005.
          Where Devotion Meets Tradition — serving thousands of families across Dilsukhnagar
          with pure, premium religious items for every occasion.
        </p>
        <div class="footer-socials">
          <a href="${STORE.instagram}" target="_blank" class="social-link instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="${STORE.whatsappGroup}" target="_blank" class="social-link whatsapp" title="WhatsApp Community"><i class="fab fa-whatsapp"></i></a>
          <a href="tel:${STORE.phone1}" class="social-link call" title="Call Us"><i class="fas fa-phone-alt"></i></a>
        </div>
      </div>
      <div class="footer-col">
        <h4 class="footer-heading">Quick Links</h4>
        <ul class="footer-links">
          <li><a href="index.html">Home</a></li>
          <li><a href="shop.html">All Products</a></li>
          <li><a href="about.html">About Us</a></li>
          <li><a href="reviews.html">⭐ Review Us</a></li>
          <li><a href="contact.html">Contact</a></li>
          <li><a href="cart.html">Cart</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4 class="footer-heading">Categories</h4>
        <ul class="footer-links">
          <li><a href="shop.html?cat=agarbatti">Agarbatti & Incense</a></li>
          <li><a href="shop.html?cat=camphor">Camphor (Kapoor)</a></li>
          <li><a href="shop.html?cat=kumkum">Kumkum & Haldi</a></li>
          <li><a href="shop.html?cat=diyas">Diyas & Lamps</a></li>
          <li><a href="shop.html?cat=idols">Idols & Vigrahas</a></li>
          <li><a href="shop.html?cat=festivals">Festival Kits</a></li>
          <li><a href="shop.html?cat=wedding">Wedding Items</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4 class="footer-heading">Contact Us</h4>
        <div class="footer-contact-list">
          <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>Konark Theatre Ln, opp. Sanjay Super Market, Dilsukhnagar, Hyderabad – 500060</span>
          </div>
          <div class="contact-item">
            <i class="fas fa-phone-alt"></i>
            <span>
              <a href="tel:+919110582086">+91 91105 82086</a><br>
              <a href="tel:+919849985423">+91 98499 85423</a><br>
              <a href="tel:+919182814818">+91 91828 14818</a><br>
              <a href="tel:+919014758196">+91 90147 58196</a>
            </span>
          </div>
          <div class="contact-item">
            <i class="fas fa-clock"></i>
            <span>
              Mon–Sat: 8:00 AM – 10:00 PM<br>
              Sunday: 8:30 AM – 10:00 PM<br>
              <span style="color:var(--saffron);font-weight:600">🎉 Festivals: 7:00 AM – 11:30 PM</span>
            </span>
          </div>
        </div>
        <a href="${STORE.whatsappGroup}" target="_blank" class="footer-whatsapp-btn">
          <i class="fab fa-whatsapp"></i> Join WhatsApp Community
        </a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
      <span>© 2005–2025 Sri Manikanta Pooja Stores, Hyderabad. All rights reserved.</span>
      <div class="footer-bottom-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Shipping Policy</a>
      </div>
    </div>
  </div>`;
}

// ── Floating Action Buttons ───────────────────────────────────
function renderFloatingActions() {
  return `
  <div class="floating-actions">
    <a href="https://wa.me/${STORE.whatsapp2}?text=Hello!%20I%20visited%20your%20website%20and%20need%20help%20with%20pooja%20items."
       target="_blank" class="float-btn float-btn-whatsapp" title="Chat with us">
      <i class="fab fa-whatsapp"></i>
      <span class="float-btn-label">Chat with us</span>
    </a>
    <a href="tel:${STORE.phone2}" class="float-btn float-btn-call" title="Call us now">
      <i class="fas fa-phone-alt"></i>
      <span class="float-btn-label">Call us now</span>
    </a>
    <a href="${STORE.whatsappGroup}" target="_blank" class="float-btn float-btn-community" title="Join our WhatsApp community" style="background-color: #25D366; color: white;">
      <i class="fas fa-users"></i>
      <span class="float-btn-label">Join our WhatsApp community</span>
    </a>
    <button class="float-btn float-btn-top" id="scroll-top" title="Back to top" style="opacity:0;transition:opacity 0.3s">
      <i class="fas fa-chevron-up"></i>
    </button>
  </div>
  <!-- Floating Cart Button (Left Side) -->
  <a href="cart.html" class="floating-cart-btn" id="floating-cart-btn" title="View Cart">
    <i class="fas fa-shopping-cart"></i>
    <span class="floating-cart-count" id="floating-cart-count">0</span>
    <span class="floating-cart-label">Cart</span>
  </a>`;
}



// ── Product Card Renderer ─────────────────────────────────────
function renderProductCard(p) {
  const discount = getDiscount(p.price, p.originalPrice);
  return `
  <div class="product-card reveal" onclick="location.href='product.html?id=${p.id}'">
    <div class="card-img-wrap">
      <img src="${p.image}" alt="${p.name}" loading="lazy" onerror="this.src='https://picsum.photos/seed/${p.id}99/400/400'">
      <div class="card-badges">
        ${p.badge === 'sale'    ? `<span class="badge badge-sale">-${discount}%</span>` : ''}
        ${p.badge === 'new'     ? `<span class="badge badge-new">NEW</span>` : ''}
        ${p.badge === 'popular' ? `<span class="badge badge-popular">⭐ Popular</span>` : ''}
      </div>
      <div class="card-actions">
        <button class="card-action-btn" title="Quick View" onclick="event.stopPropagation();location.href='product.html?id=${p.id}'">
          <i class="fas fa-eye"></i>
        </button>
        <button class="card-action-btn" title="Wishlist" onclick="event.stopPropagation();toggleWishlistCard(${p.id}, this)">
          <i class="${Wishlist.includes(p.id) ? 'fas' : 'far'} fa-heart" style="${Wishlist.includes(p.id) ? 'color:#e53935' : ''}"></i>
        </button>
      </div>
    </div>
    <div class="card-body">
      <div class="card-category">${p.categoryName}</div>
      <h3 class="card-title">${p.name}</h3>
      <div class="card-rating">
        <span class="stars">${renderStars(p.rating)}</span>
        <span class="rating-count">(${p.reviews})</span>
      </div>
      <div class="card-price">
        <span class="price-current">${formatPrice(p.price)}</span>
        <span class="price-original">${formatPrice(p.originalPrice)}</span>
        <span class="price-discount">${discount}% off</span>
      </div>
      <button class="card-add-btn" onclick="event.stopPropagation();addToCartFromCard(${p.id}, this)">
        <i class="fas fa-shopping-bag"></i> Add to Cart
      </button>
    </div>
  </div>`;
}

function addToCartFromCard(id, btn) {
  const product = getProductById(id);
  if (!product) return;
  
  let variantName = '';
  let itemToAdd = { ...product };
  
  if (product.sizes && product.sizes.length > 0) {
    const s = product.sizes[0];
    if (typeof s === 'object') {
      variantName = s.name;
      itemToAdd.price = s.price;
      if (s.image) itemToAdd.image = s.image;
    } else {
      variantName = s;
    }
  }
  
  Cart.add(itemToAdd, 1, variantName);
  
  btn.innerHTML = '<i class="fas fa-check"></i> Added!';
  btn.classList.add('added');
  setTimeout(() => {
    btn.innerHTML = '<i class="fas fa-shopping-bag"></i> Add to Cart';
    btn.classList.remove('added');
  }, 1800);
}

function toggleWishlistCard(id, btn) {
  const isNowInWishlist = Wishlist.toggle(id);
  const icon = btn.querySelector('i');
  if (isNowInWishlist) {
    icon.className = 'fas fa-heart';
    icon.style.color = '#e53935';
    btn.style.background = 'rgba(229,57,53,0.12)';
  } else {
    icon.className = 'far fa-heart';
    icon.style.color = '';
    btn.style.background = '';
  }
}

// ── Drawer Controls ───────────────────────────────────────────
function openDrawer() {
  document.getElementById('side-drawer')?.classList.add('open');
  document.getElementById('drawer-backdrop')?.classList.add('open');
  document.body.style.overflow = 'hidden';
  // Update user info in drawer
  const user = typeof AuthManager !== 'undefined' ? AuthManager.getUser() : null;
  const userInfo = document.getElementById('drawer-user-info');
  const logoutBtn = document.getElementById('drawer-logout-btn');
  const loginBtn  = document.getElementById('drawer-login-btn');
  if (userInfo && user) {
    userInfo.innerHTML = `
      <div class="drawer-avatar"><i class="fas fa-user-circle"></i></div>
      <div>
        <div class="drawer-hello">Hello, ${user.full_name ? user.full_name.split(' ')[0] : 'User'}!</div>
        <a href="dashboard/profile.html" class="drawer-signin-link">View Profile &rsaquo;</a>
      </div>`;
    if (logoutBtn) logoutBtn.style.display = 'flex';
    if (loginBtn)  loginBtn.style.display  = 'none';
  } else {
    if (logoutBtn) logoutBtn.style.display = 'none';
    if (loginBtn)  loginBtn.style.display  = 'flex';
  }
}
function closeDrawer() {
  document.getElementById('side-drawer')?.classList.remove('open');
  document.getElementById('drawer-backdrop')?.classList.remove('open');
  document.body.style.overflow = '';
}
function handleDrawerLogout() {
  if (typeof AuthManager !== 'undefined') AuthManager.logout();
  closeDrawer();
}

function handleSwitchAccount(e) {
  if (e) e.preventDefault();
  if (typeof AuthManager !== 'undefined') {
    AuthManager.logout();
  } else {
    localStorage.removeItem('smps_user');
    localStorage.removeItem('smps_token');
    window.location.href = 'login.html';
  }
}

// ── Init ──────────────────────────────────────────────────────
function initSite(activePage = '') {
  // ── Inject mobile.css once (works for all pages automatically) ──
  if (!document.getElementById('mobile-css')) {
    const mLink = document.createElement('link');
    mLink.id   = 'mobile-css';
    mLink.rel  = 'stylesheet';
    // Resolve path relative to current page (works in subdirs too)
    const depth = (window.location.pathname.match(/\//g) || []).length - 1;
    const prefix = '../'.repeat(Math.max(0, depth));
    mLink.href = prefix + 'css/mobile.css?v=5';
    document.head.appendChild(mLink);
  }

  Cart.load();
  Wishlist.load();

  // Inject header
  const headerEl = document.getElementById('site-header');
  if (headerEl) {
    headerEl.innerHTML = renderHeader(activePage);

    // Update Header Profile Dropdown User Info
    const user = typeof AuthManager !== 'undefined' ? AuthManager.getUser() : null;
    const headerUserName = document.getElementById('header-user-name');
    const headerProfileLink = document.getElementById('header-profile-link');
    const headerOrdersLink = document.getElementById('header-orders-link');
    const headerLoginLink = document.getElementById('header-login-link');
    const headerLogoutLink = document.getElementById('header-logout-link');

    if (user && headerUserName) {
      headerUserName.textContent = `Hello, ${user.full_name ? user.full_name.split(' ')[0] : 'User'}!`;
      if (headerProfileLink) headerProfileLink.style.display = 'flex';
      if (headerOrdersLink) headerOrdersLink.style.display = 'flex';
      if (headerLogoutLink) headerLogoutLink.style.display = 'flex';
      if (headerLoginLink) headerLoginLink.style.display = 'none';
    } else {
      if (headerUserName) headerUserName.textContent = 'Hello, Guest';
      if (headerProfileLink) headerProfileLink.style.display = 'none';
      if (headerOrdersLink) headerOrdersLink.style.display = 'none';
      if (headerLogoutLink) headerLogoutLink.style.display = 'none';
      if (headerLoginLink) headerLoginLink.style.display = 'flex';
    }

    // All-menu drawer trigger
    document.getElementById('all-menu-btn')?.addEventListener('click', openDrawer);

    // ── Mobile Profile Dropdown: click/tap toggle ──────────────
    // On touch devices CSS :hover doesn't work, so we use a click toggle
    const profileDropdown = document.getElementById('header-profile-dropdown');
    if (profileDropdown) {
      const profileBtn = profileDropdown.querySelector('.cart-btn');
      profileBtn?.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        // Toggle .open class which shows the dropdown via CSS
        profileDropdown.classList.toggle('open');
      });
      // Close dropdown when clicking anywhere outside it
      document.addEventListener('click', function (e) {
        if (!profileDropdown.contains(e.target)) {
          profileDropdown.classList.remove('open');
        }
      });
      // Close dropdown when any link inside it is clicked
      profileDropdown.querySelectorAll('a, button').forEach(el => {
        el.addEventListener('click', () => {
          profileDropdown.classList.remove('open');
        });
      });
    }
    // Hamburger (mobile) toggle
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobile-nav');
    hamburger?.addEventListener('click', function () {
      this.classList.toggle('open');
      this.setAttribute('aria-expanded', this.classList.contains('open'));
      mobileNav?.classList.toggle('open');
    });
    mobileNav?.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        hamburger?.classList.remove('open');
        hamburger?.setAttribute('aria-expanded', 'false');
        mobileNav?.classList.remove('open');
      });
    });
    // Search toggle
    document.getElementById('search-toggle')?.addEventListener('click', function (e) {
      e.stopPropagation();
      const wrap = document.getElementById('search-wrap');
      wrap.classList.toggle('active');
      const headerActions = wrap.closest('.header-actions') || wrap.parentElement;
      if (wrap.classList.contains('active')) {
        document.getElementById('header-search')?.focus();
        headerActions?.classList.add('search-open');
      } else {
        headerActions?.classList.remove('search-open');
      }
    });
    document.getElementById('header-search')?.addEventListener('keypress', function (e) {
      if (e.key === 'Enter' && this.value.trim()) {
        window.location.href = `shop.html?q=${encodeURIComponent(this.value.trim())}`;
      }
    });
    document.addEventListener('click', (e) => {
      const wrap = document.getElementById('search-wrap');
      if (wrap && !wrap.contains(e.target)) {
        wrap.classList.remove('active');
        const headerActions = wrap.closest('.header-actions') || wrap.parentElement;
        headerActions?.classList.remove('search-open');
        const enqBtn = document.querySelector('.header-enquiry-btn');
        if (enqBtn) { enqBtn.style.opacity = ''; enqBtn.style.pointerEvents = ''; enqBtn.style.width = ''; enqBtn.style.overflow = ''; enqBtn.style.padding = ''; enqBtn.style.margin = ''; }
      }
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') { closeStoreMapBtn(); closeDrawer(); }
    });
    initHeaderScroll();
  }

  // Store map modal
  if (!document.getElementById('store-map-modal')) {
    const mapModal = document.createElement('div');
    mapModal.id = 'store-map-modal';
    mapModal.className = 'map-modal-backdrop';
    mapModal.setAttribute('onclick', 'closeStoreMap(event)');
    mapModal.innerHTML = `
      <div class="map-modal-box">
        <div class="map-modal-header">
          <h3><i class="fas fa-map-marker-alt"></i> Visit Our Store</h3>
          <button class="map-modal-close" onclick="closeStoreMapBtn()"><i class="fas fa-times"></i></button>
        </div>
        <div class="map-modal-body">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7615.796686244467!2d78.51863469196398!3d17.36862398406134!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb9992e24cfac1%3A0x84d6068085b897e0!2sSri%20manikanta%20pooja%20stores!5e0!3m2!1sen!2sin!4v1782628485698!5m2!1sen!2sin" width="100%" height="380" style="border:0;border-radius:12px" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          <div class="map-modal-info">
            <p><i class="fas fa-map-marker-alt"></i> Konark Theatre Ln, opp. Sanjay Super Market, Dilsukhnagar, Hyderabad – 500060</p>
            <a href="https://maps.app.goo.gl/y4TFYSYQsG3ZiySJ8" target="_blank" class="btn btn-primary btn-sm" style="margin-top:10px">
              <i class="fas fa-directions"></i> Get Directions
            </a>
          </div>
        </div>
      </div>`;
    document.body.appendChild(mapModal);
  }

  // Inject footer
  const footerEl = document.getElementById('site-footer');
  if (footerEl) footerEl.innerHTML = renderFooter();

  // Inject floating actions
  const floatEl = document.getElementById('floating-actions');
  if (floatEl) {
    floatEl.innerHTML = renderFloatingActions();
    const scrollTopBtn = document.getElementById('scroll-top');
    window.addEventListener('scroll', () => {
      if (scrollTopBtn) scrollTopBtn.style.opacity = window.scrollY > 300 ? '1' : '0';
    }, { passive: true });
    scrollTopBtn?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  updateCartBadge();

  if (!document.getElementById('toast-container')) {
    const tc = document.createElement('div');
    tc.id = 'toast-container';
    document.body.appendChild(tc);
  }

  setTimeout(initScrollReveal, 100);
}

// ── Helpers ───────────────────────────────────────────────────
function formatPrice(price) {
  if (!price) return '';
  return '₹' + Number(price).toLocaleString('en-IN');
}

function getDiscount(price, originalPrice) {
  if (!originalPrice || originalPrice <= price) return 0;
  return Math.round(((originalPrice - price) / originalPrice) * 100);
}

function renderStars(rating) {
  let stars = '';
  for (let i = 1; i <= 5; i++) {
    if (rating >= i) stars += '<i class="fas fa-star"></i>';
    else if (rating >= i - 0.5) stars += '<i class="fas fa-star-half-alt"></i>';
    else stars += '<i class="far fa-star"></i>';
  }
  return stars;
}

function getProductById(id) {
  return typeof PRODUCTS !== 'undefined' ? PRODUCTS.find(p => p.id == id) : null;
}
