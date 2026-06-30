// ============================================================
// SRI MANIKANTA POOJA STORES — PRODUCT DETAIL PAGE JS
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
  initSite('shop.html');
  initProductDetail();
});

let selectedVariant = '';
let selectedVariantObj = null;
let quantity = 1;
let currentProduct = null;

async function initProductDetail() {
  const params  = new URLSearchParams(window.location.search);
  const id      = params.get('id');
  let product = null;

  try {
    const res = await fetch(`api/products/detail.php?id=${id}`);
    const data = await res.json();
    if (data.success && data.product) product = data.product;
  } catch (e) {
    console.log("Using static products", e);
  }
  
  if (!product) product = getProductById(id);

  if (!product) {
    document.getElementById('product-detail')?.insertAdjacentHTML('afterbegin', `
      <div class="empty-state" style="padding:80px 0">
        <div class="empty-state-icon">😕</div>
        <h3>Product not found</h3>
        <p>The product you're looking for doesn't exist.</p>
        <a href="shop.html" class="btn btn-primary">Browse All Products</a>
      </div>`);
    return;
  }

  currentProduct = product;

  // ── Normalise API fields → camelCase so renderProductDetail works ──
  // The DB API returns snake_case; the static PRODUCTS array uses camelCase.
  if (product.original_price  !== undefined) product.originalPrice  = parseFloat(product.original_price)  || 0;
  if (product.category_name   !== undefined) product.categoryName   = product.category_name;
  if (product.review_count    !== undefined) product.reviews        = parseInt(product.review_count)       || 0;
  if (product.telugu_name     !== undefined) product.telugu         = product.telugu_name;
  if (!product.images || !product.images.length) product.images = product.image ? [product.image] : [];
  if (!product.image)  product.image = product.images[0] || '';
  if (!product.tags)   product.tags  = [];
  product.price         = parseFloat(product.price)  || 0;
  product.originalPrice = product.originalPrice       || product.price;
  product.rating        = parseFloat(product.rating)  || 0;
  product.reviews       = product.reviews             || 0;
  product.categoryName  = product.categoryName        || product.category || '';

  // Page title / meta
  document.title = `${product.name} | Sri Manikanta Pooja Stores`;

  // Breadcrumb
  const bc = document.getElementById('product-breadcrumb');
  if (bc) bc.innerHTML = `
    <a href="index.html">Home</a>
    <i class="fas fa-chevron-right"></i>
    <a href="shop.html">Shop</a>
    <i class="fas fa-chevron-right"></i>
    <a href="shop.html?cat=${product.category}">${product.categoryName || product.category}</a>
    <i class="fas fa-chevron-right"></i>
    <span>${product.name}</span>`;

  renderProductDetail(product);
  renderRelated(product);
}

function renderProductDetail(p) {
  const discount = getDiscount(p.price, p.originalPrice);

  // Gallery
  let allThumbs = [...p.images];
  if (p.sizes && p.sizes.length > 0) {
    p.sizes.forEach(s => {
      if (typeof s === 'object' && s.image && !allThumbs.includes(s.image)) {
        allThumbs.push(s.image);
      }
    });
  }

  const mainImg = document.getElementById('main-product-img');
  if (mainImg) mainImg.src = allThumbs[0] || p.image;
  const thumbRow = document.getElementById('thumb-row');
  if (thumbRow) {
    thumbRow.innerHTML = allThumbs.map((img, i) => `
      <div class="thumb${i===0?' active':''}" onclick="setMainImg('${img}', this)">
        <img src="${img}" alt="View ${i+1}" loading="lazy" onerror="this.src='${p.image}'">
      </div>`).join('');
  }

  // Product info
  document.getElementById('product-category-link').textContent = p.categoryName;
  document.getElementById('product-category-link').href = `shop.html?cat=${p.category}`;
  document.getElementById('product-title').textContent   = p.name;
  document.getElementById('product-telugu').textContent  = p.telugu;
  document.getElementById('product-rating-stars').innerHTML = renderStars(p.rating);
  document.getElementById('product-rating-num').textContent  = p.rating;
  document.getElementById('product-review-count').textContent = `(${p.reviews} reviews)`;
  document.getElementById('product-price').textContent        = formatPrice(p.price);
  document.getElementById('product-price-orig').textContent   = formatPrice(p.originalPrice);
  document.getElementById('product-price-save').textContent   = `${discount}% off — Save ${formatPrice(p.originalPrice - p.price)}`;
  document.getElementById('product-desc').textContent         = p.description;

  // Size options (Variants)
  const optionsContainer = document.getElementById('product-size-options');
  if (optionsContainer && p.sizes && p.sizes.length > 0) {
    const isObj = typeof p.sizes[0] === 'object';
    optionsContainer.innerHTML = `<div class="variant-grid">` + p.sizes.map((s, i) => {
      if (isObj) {
        return `
        <div class="variant-box${i===0?' active':''}" onclick="selectVariant(${i}, this)">
          <span class="var-name">${s.name}</span>
          <span class="var-price">₹${s.price} ${s.original_price ? `<span class="var-orig">₹${s.original_price}</span>` : ''}</span>
        </div>`;
      } else {
        return `<button class="product-option-btn${i===0?' active':''}" onclick="selectVariantStr('${s}', this)">${s}</button>`;
      }
    }).join('') + `</div>`;
    
    if (isObj) {
      selectVariant(0, optionsContainer.querySelector('.variant-box'));
    } else {
      selectedVariant = p.sizes[0];
      selectedVariantObj = null;
    }
  } else {
    optionsContainer.innerHTML = '';
    selectedVariant = '';
    selectedVariantObj = null;
  }

  // Tags
  const tagsEl = document.getElementById('product-tags');
  if (tagsEl) {
    tagsEl.innerHTML = p.tags.map(t => `<span class="chip">#${t}</span>`).join('');
  }

  // Wishlist initialization
  const wlBtn = document.getElementById('detail-wishlist-btn');
  if (wlBtn) {
    wlBtn.style.display = 'flex';
    const wlIcon = wlBtn.querySelector('i');
    if (Wishlist.includes(p.id)) {
      wlIcon.className = 'fas fa-heart';
      wlIcon.style.color = '#e53935';
    } else {
      wlIcon.className = 'far fa-heart';
      wlIcon.style.color = '';
    }
  }
}

function setMainImg(src, thumbEl) {
  document.getElementById('main-product-img').src = src;
  document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
  if (thumbEl) thumbEl.classList.add('active');
  
  if (currentProduct && currentProduct.sizes) {
    const vIndex = currentProduct.sizes.findIndex(s => s.image === src);
    if (vIndex !== -1) {
      const boxes = document.querySelectorAll('.variant-box');
      if (boxes[vIndex] && !boxes[vIndex].classList.contains('active')) {
        selectedVariantObj = currentProduct.sizes[vIndex];
        selectedVariant = selectedVariantObj.name;
        
        document.querySelectorAll('.variant-box').forEach(b => b.classList.remove('active'));
        boxes[vIndex].classList.add('active');
        
        document.getElementById('product-price').textContent = formatPrice(selectedVariantObj.price);
        if (selectedVariantObj.original_price) {
          document.getElementById('product-price-orig').textContent = formatPrice(selectedVariantObj.original_price);
          const discount = getDiscount(selectedVariantObj.price, selectedVariantObj.original_price);
          document.getElementById('product-price-save').textContent = `${discount}% off — Save ${formatPrice(selectedVariantObj.original_price - selectedVariantObj.price)}`;
        } else {
          document.getElementById('product-price-orig').textContent = '';
          document.getElementById('product-price-save').textContent = '';
        }
      }
    }
  }
}

function selectVariant(index, btn) {
  selectedVariantObj = currentProduct.sizes[index];
  selectedVariant = selectedVariantObj.name;
  
  document.querySelectorAll('.variant-box').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  
  // Update UI with variant specific details
  document.getElementById('product-price').textContent = formatPrice(selectedVariantObj.price);
  if (selectedVariantObj.original_price) {
    document.getElementById('product-price-orig').textContent = formatPrice(selectedVariantObj.original_price);
    const discount = getDiscount(selectedVariantObj.price, selectedVariantObj.original_price);
    document.getElementById('product-price-save').textContent = `${discount}% off — Save ${formatPrice(selectedVariantObj.original_price - selectedVariantObj.price)}`;
  } else {
    document.getElementById('product-price-orig').textContent = '';
    document.getElementById('product-price-save').textContent = '';
  }
  
  // Swap image if variant has one
  if (selectedVariantObj.image) {
    document.getElementById('main-product-img').src = selectedVariantObj.image;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.thumb img').forEach(img => {
      if (img.getAttribute('src') === selectedVariantObj.image) {
        img.parentElement.classList.add('active');
      }
    });
  }
}

function selectVariantStr(size, btn) {
  selectedVariant = size;
  selectedVariantObj = null;
  document.querySelectorAll('.product-option-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function changeQty(delta) {
  quantity = Math.max(1, quantity + delta);
  const qi = document.getElementById('qty-input');
  if (qi) qi.value = quantity;
}

function addToCartFromDetail() {
  if (!currentProduct) return;
  
  const cartItem = { ...currentProduct };
  if (selectedVariantObj) {
    cartItem.price = selectedVariantObj.price;
    if (selectedVariantObj.image) cartItem.image = selectedVariantObj.image;
    else cartItem.image = currentProduct.images?.[0] || currentProduct.image || '';
  } else {
    cartItem.image = currentProduct.images?.[0] || currentProduct.image || '';
  }
  
  Cart.add(cartItem, quantity, selectedVariant);
  const btn = document.getElementById('add-to-cart-btn');
  if (btn) {
    btn.innerHTML = '<i class="fas fa-check"></i> Added to Cart!';
    btn.style.background = 'linear-gradient(135deg,#2E7D32,#1B5E20)';
    setTimeout(() => {
      btn.innerHTML = '<i class="fas fa-shopping-bag"></i> Add to Cart';
      btn.style.background = '';
    }, 2000);
  }
}

function buyNow() {
  if (!currentProduct) return;
  const cartItem = { ...currentProduct };
  if (selectedVariantObj) {
    cartItem.price = selectedVariantObj.price;
    if (selectedVariantObj.image) cartItem.image = selectedVariantObj.image;
    else cartItem.image = currentProduct.images?.[0] || currentProduct.image || '';
  } else {
    cartItem.image = currentProduct.images?.[0] || currentProduct.image || '';
  }
  Cart.add(cartItem, quantity, selectedVariant);
  window.location.href = 'cart.html';
}

function toggleDetailWishlist() {
  if (!currentProduct) return;
  const isNowInWishlist = Wishlist.toggle(currentProduct.id);
  const icon = document.querySelector('#detail-wishlist-btn i');
  if (isNowInWishlist) {
    icon.className = 'fas fa-heart';
    icon.style.color = '#e53935';
  } else {
    icon.className = 'far fa-heart';
    icon.style.color = '';
  }
}

function renderRelated(p) {
  const grid = document.getElementById('related-grid');
  if (!grid) return;

  // Helper: shuffle array in-place (Fisher-Yates)
  function shuffle(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
  }

  // Always use the static PRODUCTS array for recommendations.
  // Exclude the current product by NAME (safe even when API returns a different numeric id).
  const currentName = (p.name || '').trim().toLowerCase();
  const others = PRODUCTS.filter(x => x.name.trim().toLowerCase() !== currentName);

  // Determine same-category match: compare category slug (DB returns slug in p.category)
  const currentCat = (p.category || '').trim().toLowerCase();
  const sameCategory  = shuffle(others.filter(x => x.category.toLowerCase() === currentCat));
  const otherCategory = shuffle(others.filter(x => x.category.toLowerCase() !== currentCat));

  // Prioritize same category first, then fill with other categories, up to 8 total
  const mixed = [...sameCategory, ...otherCategory].slice(0, 8);

  // Final shuffle so the layout looks dynamic
  const related = shuffle(mixed);

  if (related.length === 0) {
    document.getElementById('related-section')?.style?.setProperty('display', 'none');
    return;
  }
  grid.innerHTML = related.map(r => renderProductCard(r)).join('');
  // Ensure scroll reveal fires on these newly injected cards
  setTimeout(initScrollReveal, 50);

  // ── Populate & show sticky peek bar ──────────────────────────
  const peekBar    = document.getElementById('peek-bar');
  const peekThumbs = document.getElementById('peek-thumbs');
  if (peekBar && peekThumbs) {
    peekThumbs.innerHTML = related.slice(0, 5).map(r => `
      <div style="flex-shrink:0; text-align:center; width:62px;">
        <img src="${r.image || (r.images && r.images[0]) || ''}"
             alt="${r.name}" loading="lazy"
             style="width:56px;height:56px;border-radius:10px;object-fit:cover;
                    border:2px solid #f0e0cc;display:block;margin:0 auto 3px;">
        <div style="font-size:0.62rem;color:#7a3e00;font-weight:600;
                    line-height:1.2;max-height:26px;overflow:hidden;">
          ${r.name.split(' ').slice(0,3).join(' ')}
        </div>
        <div style="font-size:0.68rem;color:#f08b43;font-weight:700;">₹${r.price}</div>
      </div>`).join('');

    // Show bar only while related section is out of viewport
    const relSec = document.getElementById('related-section');
    const showBar = () => {
      if (!relSec) return;
      const rect = relSec.getBoundingClientRect();
      if (rect.top > window.innerHeight) {
        peekBar.style.display = 'flex';
      } else {
        peekBar.style.display = 'none';
      }
    };
    setTimeout(showBar, 600);            // small delay so page settles
    window.addEventListener('scroll', showBar, { passive: true });
  }

}

