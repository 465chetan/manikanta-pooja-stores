// ============================================================
// SRI MANIKANTA POOJA STORES — AUTH MANAGER v3
// ============================================================

// ── API Base URL ──────────────────────────────────────────
const _scripts = document.getElementsByTagName('script');
let _basePath = '';
for (let s of _scripts) {
  if (s.src.includes('/js/auth.js')) {
    _basePath = new URL(s.src).pathname.replace('/js/auth.js', '');
    break;
  }
}
const _SMPS_BASE = window.location.origin + _basePath;

// If served from http-server (port 8091), the PHP API runs on Laragon at /smps
// Otherwise use relative path (production or Laragon-served directly)
const API_BASE = (window.location.port === '8091')
  ? 'http://localhost/smps/api'
  : _SMPS_BASE + '/api';

const AuthManager = {
  // ── Token Storage ──────────────────────────────────────────
  getToken() { return localStorage.getItem('smps_token'); },
  getUser()  { try { return JSON.parse(localStorage.getItem('smps_user') || 'null'); } catch { return null; } },
  isLoggedIn() { return !!this.getToken() && !!this.getUser(); },

  save(token, user) {
    localStorage.setItem('smps_token', token);
    localStorage.setItem('smps_user', JSON.stringify(user));
  },

  clear() {
    localStorage.removeItem('smps_token');
    localStorage.removeItem('smps_user');
  },

  // ── API Helper ─────────────────────────────────────────────
  async _request(endpoint, method = 'GET', body = null) {
    const opts = {
      method,
      cache: 'no-store',
      headers: {
        'Content-Type': 'application/json',
        ...(this.getToken() ? { 'Authorization': `Bearer ${this.getToken()}` } : {}),
      },
    };
    if (body) opts.body = JSON.stringify(body);
    try {
      const sep = endpoint.includes('?') ? '&' : '?';
      const res  = await fetch(`${API_BASE}/${endpoint}${sep}_t=${Date.now()}`, opts);
      const data = await res.json();
      return { ...data, httpStatus: res.status };
    } catch (err) {
      return { success: false, message: 'API Error: ' + err.message };
    }
  },

  // ── Register ───────────────────────────────────────────────
  async register({ full_name, mobile, email, password }) {
    const data = await this._request('auth/register.php', 'POST', { full_name, mobile, email, password });
    if (data.success) this.save(data.token, data.user);
    return data;
  },

  // ── Login (by email or mobile) ─────────────────────────────
  async login(emailOrMobile, password) {
    const isEmail = emailOrMobile.includes('@');
    const payload = isEmail
      ? { email: emailOrMobile, password }
      : { mobile: emailOrMobile, password };
    const data = await this._request('auth/login.php', 'POST', payload);
    if (data.success) this.save(data.token, data.user);
    return data;
  },

  // ── Logout ─────────────────────────────────────────────────
  logout() {
    this.clear();
    window.location.href = _SMPS_BASE + '/login.html';
  },

  // ── Get Current User ───────────────────────────────────────
  async me() {
    return this._request('auth/me.php');
  },

  // ── Require Login (redirect if not logged in) ──────────────
  requireLogin() {
    if (!this.isLoggedIn()) {
      const redirect = encodeURIComponent(window.location.pathname + window.location.search);
      window.location.href = `${_SMPS_BASE}/login.html?redirect=${redirect}`;
      return false;
    }
    return true;
  },

  // ── Update header UI based on login state ─────────────────
  updateHeader() {
    const user = this.getUser();
    const loginLinks   = document.querySelectorAll('.nav-login-link, [data-auth="login"]');
    const accountLinks = document.querySelectorAll('.nav-account-link, [data-auth="account"]');
    if (user) {
      loginLinks.forEach(el => el.style.display = 'none');
      accountLinks.forEach(el => {
        el.style.display = '';
        const nameEl = el.querySelector('.user-name');
        if (nameEl) nameEl.textContent = user.full_name.split(' ')[0];
      });
    } else {
      loginLinks.forEach(el => el.style.display = '');
      accountLinks.forEach(el => el.style.display = 'none');
    }
  },
};

// ── Forgot Password ─────────────────────────────────────────
const ForgotPassword = {
  async sendOTP(mobile) {
    return AuthManager._request('auth/forgot-password.php', 'POST', { mobile });
  },
  async resetPassword(mobile, otp, password) {
    return AuthManager._request('auth/reset-password.php', 'POST', { mobile, otp, password });
  },
};

// ── Dashboard API Client ─────────────────────────────────────
const DashboardAPI = {
  async getSummary()       { return AuthManager._request('user/dashboard.php'); },
  async getOrders(page=1)  { return AuthManager._request(`orders/list.php?page=${page}`); },
  async getOrderDetail(id) { return AuthManager._request(`orders/detail.php?id=${id}`); },
  async getProfile()       { return AuthManager._request('user/profile.php'); },
  async updateProfile(d)   { return AuthManager._request('user/profile.php', 'PUT', d); },
  async getAddresses()     { return AuthManager._request('user/addresses.php'); },
  async addAddress(d)      { return AuthManager._request('user/addresses.php', 'POST', d); },
  async updateAddress(d)   { return AuthManager._request('user/addresses.php', 'PUT', d); },
  async deleteAddress(id)  { return AuthManager._request(`user/addresses.php?id=${id}`, 'DELETE'); },
};

// ── Checkout API Client ───────────────────────────────────────
const CheckoutAPI = {
  async createOrder(data) { return AuthManager._request('orders/create.php', 'POST', data); },
};

// ── Admin API Client ──────────────────────────────────────────
const AdminAPI = {
  _token:   () => localStorage.getItem('smps_admin_token'),
  _headers: () => ({ 'Content-Type': 'application/json', 'Authorization': `Bearer ${AdminAPI._token()}` }),

  async login(email, password) {
    try {
      const res  = await fetch(`${API_BASE}/admin/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });
      const data = await res.json();
      if (data.success) localStorage.setItem('smps_admin_token', data.token);
      return data;
    } catch(e) {
      return { success: false, message: 'Cannot connect to server. Please try again later.' };
    }
  },

  logout() {
    localStorage.removeItem('smps_admin_token');
    window.location.href = _SMPS_BASE + '/admin/login.html';
  },

  isLoggedIn()  { return !!this._token(); },

  requireLogin() {
    if (!this.isLoggedIn()) {
      window.location.href = _SMPS_BASE + '/admin/login.html';
      return false;
    }
    return true;
  },

  async _req(endpoint, method = 'GET', body = null) {
    const opts = { method, cache: 'no-store', headers: this._headers() };
    if (body) opts.body = JSON.stringify(body);
    try {
      const sep = endpoint.includes('?') ? '&' : '?';
      const res  = await fetch(`${API_BASE}/${endpoint}${sep}_t=${Date.now()}`, opts);
      const data = await res.json();
      if (res.status === 401) { this.logout(); return data; }
      return data;
    } catch(e) {
      return { success: false, message: 'Network error: ' + (e.message || String(e)) };
    }
  },

  async getDashboard()                           { return this._req('admin/dashboard.php'); },
  async getOrders(params = '')                   { return this._req(`admin/orders/list.php${params}`); },
  async updateOrderStatus(orderId, status, notes='', verifyPayment=false) { return this._req('admin/orders/list.php', 'PUT', { order_id: orderId, status, admin_notes: notes, verify_payment: verifyPayment }); },
  async getProducts(params = '')                 { return this._req(`admin/products/list.php${params}`); },
  async createProduct(data)                      { return this._req('admin/products/list.php', 'POST', data); },
  async updateProduct(data)                      { return this._req('admin/products/list.php', 'PUT', data); },
  async deleteProduct(id)                        { return this._req(`admin/products/list.php?id=${id}`, 'DELETE'); },
  async getCustomers(params = '')                { return this._req(`admin/customers/list.php${params}`); },

  async uploadImage(formData) {
    const res = await fetch(`${API_BASE}/admin/upload.php`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${this._token()}` },
      body: formData
    });
    return res.json();
  },
};

// ── Initialize auth state in header on every page ─────────────
document.addEventListener('DOMContentLoaded', async () => {
  AuthManager.updateHeader();
  
  // Update and hide/show pending orders badge dynamically
  if (window.location.pathname.includes('/admin/')) {
    const badge = document.getElementById('pending-badge');
    if (badge && AdminAPI.isLoggedIn()) {
      try {
        const res = await AdminAPI.getDashboard();
        if (res.success && res.stats) {
          const pending = Number(res.stats.pending || res.stats.pending_orders || 0);
          badge.textContent = pending;
          badge.style.display = pending > 0 ? 'inline-block' : 'none';
        } else {
          badge.style.display = 'none';
        }
      } catch (e) {
        badge.style.display = 'none';
      }
    } else if (badge) {
      badge.style.display = 'none';
    }
  }
});
