# Sri Manikanta Pooja Stores — Complete Deployment Guide
## From Local Development to Live cPanel Hosting

---

## Step 1 — Prepare Your Hosting (cPanel)

### 1.1 Login to cPanel
Go to `https://yourdomain.com/cpanel` or your hosting provider's control panel.

### 1.2 Create a MySQL Database
1. Go to **cPanel → MySQL Databases**
2. Create a new database: `smps_db`
3. Create a new database user: `smps_user` with a strong password
4. Add `smps_user` to `smps_db` with **ALL PRIVILEGES**

> **Save these credentials — you'll need them in config.php!**

### 1.3 Set PHP Version to 8.1+
1. Go to **cPanel → MultiPHP Manager**
2. Select your domain and set PHP version to **8.1** or **8.2**
3. Click Apply

### 1.4 Enable SSL (Free)
1. Go to **cPanel → SSL/TLS → Let's Encrypt**
2. Install SSL for your domain
3. Enable "Force HTTPS" redirect

---

## Step 2 — Configure the Application

### 2.1 Edit `api/config/config.php`

Open the file and update ALL these values:

```php
define('APP_URL',  'https://yourdomain.com'); // ← Your actual domain

define('DB_HOST', 'localhost');
define('DB_NAME', 'youraccount_smps_db');  // ← cPanel prefixes DB names!
define('DB_USER', 'youraccount_smps_user'); // ← Same prefix for username
define('DB_PASS', 'YOUR_STRONG_PASSWORD');

define('JWT_SECRET', 'abc123...'); // ← Generate: openssl rand -hex 32

// Razorpay (get from razorpay.com/dashboard)
define('RAZORPAY_KEY_ID',     'rzp_live_YOUR_KEY');
define('RAZORPAY_KEY_SECRET', 'YOUR_SECRET');

// Email (use Zoho Mail free account)
define('MAIL_HOST',     'smtp.zoho.in');
define('MAIL_USERNAME', 'orders@yourdomain.com');
define('MAIL_PASSWORD', 'your_zoho_app_password');
define('MAIL_FROM_EMAIL', 'orders@yourdomain.com');

define('OWNER_EMAIL',    'your_personal_email@gmail.com');
define('APP_ENV', 'production'); // ← Change this!
```

> ⚠️ **IMPORTANT:** cPanel automatically prefixes database names and usernames with your cPanel username. Example: if your cPanel username is `manistore`, your DB name becomes `manistore_smps_db`

---

## Step 3 — Upload Files to cPanel

### Option A — File Manager (Easy)
1. Compress your entire project folder as a `.zip` file
2. Go to **cPanel → File Manager → public_html**
3. Upload the `.zip` file
4. Right-click → Extract

### Option B — FTP (Faster for large projects)
Use FileZilla or WinSCP:
- Host: `ftp.yourdomain.com`
- Username: your cPanel username
- Password: your cPanel password
- Port: 21
- Upload to `/public_html/`

### File Structure on Server
```
public_html/
├── index.html          ← Homepage
├── shop.html           ← Shop page
├── login.html          ← Login page
├── register.html       ← Register page
├── setup.php           ← Run ONCE then DELETE
├── api/                ← Backend PHP APIs
│   ├── config/
│   │   ├── config.php  ← ⚠️ Update this!
│   │   └── database.php
│   ├── auth/
│   ├── products/
│   ├── orders/
│   ├── payments/
│   ├── user/
│   └── admin/
├── admin/              ← Admin panel HTML
├── dashboard/          ← Customer dashboard HTML
├── database/
│   └── schema.sql      ← Database structure
├── uploads/
│   └── products/       ← Product image uploads
└── logs/               ← PHP error logs
```

---

## Step 4 — Install the Database

### Method A — Use Setup Wizard (Easiest)
1. Visit: `https://yourdomain.com/setup.php`
2. Check all green ticks
3. Click "Install Database & Create Tables"
4. **Delete setup.php immediately after!**

### Method B — phpMyAdmin (Manual)
1. Go to **cPanel → phpMyAdmin**
2. Select `smps_db` database
3. Click **Import** tab
4. Choose `database/schema.sql` file
5. Click Go

---

## Step 5 — Set File Permissions

In cPanel → File Manager or via SSH:
```bash
chmod 755 uploads/
chmod 755 uploads/products/
chmod 755 logs/
chmod 644 api/config/config.php
```

---

## Step 6 — Test Everything

### 6.1 Test the Website
- Visit `https://yourdomain.com` — homepage should load
- Visit `https://yourdomain.com/shop.html` — products should show
- Visit `https://yourdomain.com/login.html` — login page works
- Visit `https://yourdomain.com/register.html` — registration works

### 6.2 Test the Admin Panel
1. Visit `https://yourdomain.com/admin/login.html`
2. Login with:
   - Email: `admin@srimanikanta.com`
   - Password: `Admin@2025`
3. **IMMEDIATELY change the password!**

### 6.3 Test APIs
Open browser console or use a tool like Postman:
```
GET https://yourdomain.com/api/products/list.php
→ Should return list of products
```

### 6.4 Test Razorpay (Test Mode)
1. Add a product to cart
2. Proceed to checkout
3. Select "Online Payment"
4. Use Razorpay test card: `4111 1111 1111 1111` / any future date / any CVV

---

## Step 7 — Go Live Checklist

- [ ] Update `config.php` with production domain
- [ ] Change `APP_ENV` to `'production'`
- [ ] Switch Razorpay from Test to Live keys
- [ ] Delete `setup.php` from server
- [ ] Change admin password
- [ ] Enable SSL and Force HTTPS
- [ ] Test a real order end-to-end
- [ ] Verify email notifications arrive
- [ ] Test WhatsApp notification (if enabled)

---

## Maintenance Guide

### How to Add/Edit Products (Admin Panel)
1. Login to `yourdomain.com/admin/login.html`
2. Go to **Products → Add Product**
3. Fill in: Name, Telugu Name, Description, Price, Category, Images
4. Click Save

### How to Manage Orders
1. Login to admin panel
2. Go to **Orders**
3. Click on any order to update its status
4. Customer receives email/WhatsApp notification automatically

### How to Update Razorpay Keys
1. Open `api/config/config.php`
2. Update `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET`
3. Upload the updated file to server

### Monthly Maintenance Tasks
- Check `logs/` for any PHP errors
- Review and delete old rate limit records: `DELETE FROM rate_limits WHERE last_attempt < DATE_SUB(NOW(), INTERVAL 7 DAY)`
- Check DB backup in cPanel → Backup Wizard

---

## Getting a Razorpay Account

1. Go to [razorpay.com](https://razorpay.com)
2. Sign up with your business details
3. Activate your account (requires business documents)
4. Go to Settings → API Keys → Generate Key
5. Copy Key ID and Key Secret into `config.php`

**Test Mode vs Live Mode:**
- Test mode: No real money. Use test cards from Razorpay docs
- Live mode: Real payments. Switch only after full testing

---

## WhatsApp Business API Setup (Optional - ₹2,499/month)

1. Sign up at [interakt.ai](https://interakt.ai)
2. Connect your WhatsApp Business number
3. Create message templates (approval takes 2-3 days)
4. Get API key from Interakt dashboard
5. In `config.php`:
   - Set `WHATSAPP_API_ENABLED` to `true`
   - Set `WHATSAPP_API_KEY` to your Interakt API key

**Free Alternative:** The system automatically sends WhatsApp orders via the wa.me link when customers checkout — this is already working without any API cost.

---

## Important URLs

| Page | URL |
|------|-----|
| Homepage | yourdomain.com |
| Shop | yourdomain.com/shop.html |
| Login | yourdomain.com/login.html |
| Register | yourdomain.com/register.html |
| My Dashboard | yourdomain.com/dashboard/index.html |
| My Orders | yourdomain.com/dashboard/orders.html |
| **Admin Login** | **yourdomain.com/admin/login.html** |
| Admin Dashboard | yourdomain.com/admin/dashboard.html |
| Admin Products | yourdomain.com/admin/products.html |
| Admin Orders | yourdomain.com/admin/orders.html |

---

## Default Admin Credentials
```
Email:    admin@srimanikanta.com
Password: Admin@2025
```
**⚠️ Change these immediately after first login!**

---

*Last updated: June 2025 | Sri Manikanta Pooja Stores — Full-Stack eCommerce Platform*
