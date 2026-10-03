# 📦 Laraventry — Sistem Manajemen Inventori Laravel

> Sistem manajemen inventori berbasis web yang dibangun dengan Laravel 13. Dirancang untuk mengelola produk, stok, gudang, supplier, dan kategori dalam satu platform yang aman dan mudah digunakan.

---

## 📋 Daftar Isi

- [Overview](#-overview)
- [Untuk Siapa](#-untuk-siapa)
- [Fitur](#-fitur)
- [Tech Stack](#-tech-stack)
- [Development](#-development)
- [Production](#-production)
- [Kredensial Default](#-kredensial-default)
- [Arsitektur & Struktur](#-arsitektur--struktur)
- [Keamanan](#-keamanan)
- [Troubleshoot](#-troubleshoot)

---

## 🔍 Overview

**Inventori** adalah aplikasi web full-stack untuk manajemen stok barang. Sistem ini mampu melacak pergerakan stok secara real-time lintas gudang, memberikan peringatan stok rendah, dan menyimpan riwayat transaksi yang tidak dapat diubah (*immutable*).

### Detail Teknis

| Komponen | Detail |
|----------|--------|
| Framework | Laravel 13 (PHP 8.3+) |
| Database | SQLite (dev) / MySQL/PostgreSQL (prod) |
| Session | Database-backed, terenkripsi |
| Auth | Session-based, single-role (Admin) |
| Testing | PHPUnit 12 — 43 tests, 92 assertions |
| Keamanan | CSRF, XSS-safe, rate limiting, bcrypt 12 rounds |

---

## 👥 Untuk Siapa

Sistem ini cocok untuk:

- **Usaha kecil dan menengah (UKM)** yang butuh sistem inventori sederhana namun andal
- **Toko retail** yang mengelola stok di satu atau beberapa gudang
- **Developer Laravel** yang ingin mempelajari arsitektur service layer, form request, transaksi database, dan feature testing
- **Tim internal perusahaan** yang membutuhkan sistem pencatatan stok dengan audit trail

> ⚠️ Sistem ini dirancang untuk **satu role Admin**. Multi-user dengan role berbeda (admin/staf) merupakan rencana fitur lanjutan.

---

## ✨ Fitur

### Manajemen Master Data
- **Kategori Produk** — CRUD kategori dengan auto-slug, search, dan proteksi hapus (tidak bisa dihapus jika masih ada produk terkait)
- **Supplier** — CRUD supplier dengan multi-kolom search (nama, kontak, email, telepon) dan proteksi hapus
- **Gudang** — CRUD gudang dengan kode unik, search, dan proteksi hapus berlapis (ada stok / ada riwayat)

### Manajemen Produk
- **CRUD Produk** lengkap dengan harga beli & jual, SKU unik, satuan, dan status aktif/non-aktif
- **Filter multi-dimensi**: nama/SKU, kategori, supplier, status aktif, dan produk stok rendah
- **Detail produk**: tampilan stok per gudang dan 20 riwayat pergerakan stok terbaru
- **Proteksi hapus**: produk yang sudah memiliki riwayat stok tidak dapat dihapus

### Manajemen Stok
- **Stok Masuk (`in`)** — Menambah stok pada gudang tertentu
- **Stok Keluar (`out`)** — Mengurangi stok (diblokir jika stok tidak mencukupi)
- **Penyesuaian (`adjustment`)** — Set stok ke nilai absolut baru (stock opname)
- **Riwayat immutable** — Pergerakan stok yang sudah dicatat tidak dapat diubah atau dihapus
- **Pencatatan `stock_before` & `stock_after`** di setiap transaksi untuk audit trail penuh
- **Filter riwayat**: produk, gudang, tipe, rentang tanggal

### Dashboard
- Ringkasan: total produk, kategori, supplier, gudang
- Tabel **10 pergerakan stok terbaru**
- Tabel **peringatan stok rendah** (produk di bawah minimum stok)

### Autentikasi
- Login / Logout berbasis session
- **Remember Me** — session persisten
- **Redirect-if-authenticated** — user yang sudah login tidak bisa mengakses halaman login
- Semua halaman dilindungi middleware `auth`
- **Rate limiting**: maksimum 5 percobaan login per menit per email+IP

---

## 🛠 Tech Stack

```
Backend:  Laravel 13, PHP 8.3+
Database: SQLite (dev) / MySQL 8+ or PostgreSQL 14+ (prod)
Auth:     Laravel built-in session auth
Testing:  PHPUnit 12
Frontend: Bootstrap 5.3, Bootstrap Icons (via CDN)
Build:    Vite
```

---

## 💻 Development

### Prasyarat

- PHP 8.3+
- Composer
- Node.js 20+ & npm
- SQLite extension aktif

### Instalasi

```bash
# 1. Clone repository
git clone <repo-url>
cd laraproj

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Install Node dependencies
npm install

# 6. Siapkan database dan jalankan seeder
php artisan migrate:fresh --seed

# 7. Jalankan development server
npm run dev
# Di terminal lain:
php artisan serve
```

> **Atau gunakan shortcut Composer:**
> ```bash
> composer setup   # install + migrate + build
> composer dev     # serve + queue + log watcher + vite
> ```

### Menjalankan Tests

```bash
# Jalankan semua tests
php artisan test

# Atau via Composer
composer test

# Filter per kelas
php artisan test --filter=AuthTest
php artisan test --filter=CategoryTest
php artisan test --filter=ProductTest
php artisan test --filter=StockMovementTest
```

**Expected output:** `Tests: 43, Assertions: 92, Passed: 43`

### Reset Database

```bash
# Reset total + jalankan seeder ulang
php artisan migrate:fresh --seed
```

### Konfigurasi Development (`.env`)

```dotenv
APP_NAME=Inventori
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Timezone WIB
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite  # default

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

BCRYPT_ROUNDS=12
```

---

## 🚀 Production

### Prasyarat Production

- PHP 8.3+ dengan ekstensi: `pdo_mysql` / `pdo_pgsql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- MySQL 8+ atau PostgreSQL 14+
- Web server: Nginx / Apache
- Supervisor (untuk queue worker jika diperlukan)

### Langkah Deploy

```bash
# 1. Clone / pull kode ke server
git clone <repo-url> /var/www/inventori
cd /var/www/inventori

# 2. Install dependencies (tanpa dev)
composer install --no-dev --optimize-autoloader

# 3. Copy dan edit .env production
cp .env.example .env
# Edit sesuai konfigurasi production (lihat di bawah)

# 4. Generate app key
php artisan key:generate

# 5. Build frontend assets
npm ci
npm run build

# 6. Jalankan migration (TANPA --seed di production)
php artisan migrate --force

# 7. Buat user admin pertama via Tinker
php artisan tinker
>>> \App\Models\User::create(['name'=>'Admin','email'=>'admin@domain.com','password'=>bcrypt('your-strong-password')])->forceFill(['role'=>'admin'])->save();

# 8. Optimasi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Set permission storage
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Konfigurasi Production (`.env`)

```dotenv
APP_NAME=Inventori
APP_ENV=production
APP_DEBUG=false                          # ← WAJIB false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventori_db
DB_USERNAME=inventori_user
DB_PASSWORD=your-strong-db-password     # ← Gunakan password kuat

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true                    # ← WAJIB true
SESSION_SECURE_COOKIE=true             # ← Aktifkan jika pakai HTTPS

BCRYPT_ROUNDS=12

LOG_CHANNEL=daily
LOG_LEVEL=warning
```

> **⚠️ PENTING:**
> - Jangan commit file `.env` ke Git
> - Ganti semua password default sebelum deploy
> - Pastikan `APP_DEBUG=false` di production
> - Gunakan HTTPS di production

### Konfigurasi Nginx (contoh)

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/inventori/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🔑 Kredensial Default

> ⚠️ **Hanya untuk development/testing.** Ganti sebelum production.

| Field | Value |
|-------|-------|
| Email | `admin@inventori.test` |
| Password | `password` |

---

## 🏗 Arsitektur & Struktur

```
app/
├── Enums/
│   └── UserRole.php              # Enum role user (Admin)
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── DashboardController.php
│   │   ├── CategoryController.php
│   │   ├── SupplierController.php
│   │   ├── WarehouseController.php
│   │   ├── ProductController.php
│   │   └── StockMovementController.php
│   └── Requests/
│       ├── Store/UpdateCategoryRequest.php
│       ├── Store/UpdateSupplierRequest.php
│       ├── Store/UpdateWarehouseRequest.php
│       ├── Store/UpdateProductRequest.php
│       └── StoreStockMovementRequest.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Supplier.php
│   ├── Warehouse.php
│   ├── Product.php
│   ├── ProductStock.php
│   └── StockMovement.php
├── Providers/
│   └── AppServiceProvider.php    # Rate limiter config
└── Services/
    └── Inventory/
        └── StockMovementService.php  # Core business logic

database/
├── migrations/                   # 9 tabel migration
├── factories/                    # User, Category, Supplier, Warehouse, Product factories
└── seeders/
    └── DatabaseSeeder.php        # 20 produk + riwayat stok realistis

routes/
└── web.php                       # Guest routes + Auth-protected routes

tests/
└── Feature/
    ├── AuthTest.php              # 12 tests
    ├── CategoryTest.php          # 8 tests
    ├── ProductTest.php           # 9 tests
    ├── StockMovementTest.php     # 11 tests
    └── ExampleTest.php           # 3 tests
```

### Alur Transaksi Stok

```
Request → StoreStockMovementRequest (validasi)
        → StockMovementController::store()
        → StockMovementService::createMovement()
            ├── DB::transaction()
            ├── ProductStock::lockForUpdate()  ← cegah race condition
            ├── Hitung stock_before & stock_after
            ├── Validasi: stok tidak boleh negatif
            ├── Update ProductStock.quantity
            └── Create StockMovement (immutable record)
```

---

## 🔒 Keamanan

| Aspek | Implementasi |
|-------|-------------|
| CSRF | Laravel default middleware, `@csrf` di semua form |
| XSS | Blade `{{ }}` escaping di semua output |
| SQL Injection | Eloquent parameterized queries — tidak ada raw query |
| Brute Force | Rate limit 5x/menit per email+IP via `throttle:login` |
| Session Fixation | `session()->regenerate()` saat login, `invalidate()` saat logout |
| Session Encryption | `SESSION_ENCRYPT=true` |
| Password | Bcrypt 12 rounds via model cast |
| Mass Assignment | `$fillable` eksplisit di semua model; `role` dikecualikan |
| Race Condition | `DB::transaction()` + `lockForUpdate()` pada transaksi stok |

---

## 🔧 Troubleshoot

### ❌ Error: `View [layouts.app] not found`
Dashboard dan halaman lain memerlukan file layout utama.

```bash
# Cek apakah file ada
ls resources/views/layouts/app.blade.php
```
Jika tidak ada, buat file `resources/views/layouts/app.blade.php` sebagai layout Bootstrap 5.

---

### ❌ Error: `SQLSTATE[HY000]: no such table: sessions`
Session driver menggunakan database tapi tabel belum ada.

```bash
php artisan migrate
```

---

### ❌ Error: `Class "App\Models\ProductStock" not found`
Autoloader belum di-refresh setelah penambahan file.

```bash
composer dump-autoload
```

---

### ❌ Login gagal padahal kredensial benar
Kemungkinan penyebab:
1. Database belum di-seed
   ```bash
   php artisan migrate:fresh --seed
   ```
2. Cache config lama
   ```bash
   php artisan config:clear && php artisan cache:clear
   ```
3. Sudah kena rate limit (5x/menit) — tunggu 1 menit

---

### ❌ Stok keluar gagal padahal stok cukup
Pastikan `product_id` dan `warehouse_id` yang dipakai sesuai. Stok dihitung **per gudang**, bukan total semua gudang.

---

### ❌ `php artisan test` gagal dengan error database
Test menggunakan in-memory SQLite terpisah dari database development.

```bash
# Pastikan SQLite extension aktif
php -m | findstr sqlite

# Clear config cache sebelum test
php artisan config:clear
php artisan test
```

---

### ❌ Halaman muncul tapi tidak ada style (CSS tidak load)
Asset Vite belum di-build.

```bash
# Development
npm run dev

# Production
npm run build
```

---

### ❌ `APP_KEY` error saat pertama install

```bash
php artisan key:generate
```

---

### 🔄 Reset total ke kondisi awal

```bash
php artisan migrate:fresh --seed
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 📄 Lisensi

MIT License — bebas digunakan untuk keperluan pribadi, edukasi, maupun komersial.

# Laraventry Pro — Facility, People & Attendance Edition

This edition expands the inventory application into a facility/business management system.

## New modules

- Facilities: company, mall, commercial store, property/building, other.
- People: employees, workers, security guards, managers, supervisors, technicians, cleaners, sales, HR, contractors, tenants and other roles.
- Departments and work locations.
- Shifts with start/end time, grace period, break time and overnight support.
- Attendance: present, late, absent, leave, half-day and holiday.
- Automatic calculation of worked time, late minutes, early departure and overtime.
- Visitors and visitor numbers.
- Access control logs for entry/exit, gates and recorded-by user.
- Security guard fields: security company, permit number, permit expiry and guard post.
- Expanded dashboard with people, security, attendance, overtime, inventory value, expected profit, visitors, alerts and 7-day chart.

## Fresh installation

1. Copy `.env.example` to `.env`.
2. Configure MySQL/SQLite in `.env`.
3. Run `composer install`.
4. Run `php artisan key:generate`.
5. Run `php artisan migrate:fresh --seed` for the included demo database.
6. Run `npm install` and `npm run build`.
7. Start with `php artisan serve` or the Laragon Apache/Nginx setup.

Demo account after seeding:

- Email: `admin@inventori.test`
- Password: `password`

Change the demo password before using the system for real data.

## Attendance logic

A person's assigned shift supplies the scheduled start/end times. When an attendance record has check-in and check-out, the application calculates:

- worked minutes
- late minutes after the shift grace period
- early departure minutes
- overtime minutes after scheduled end

Overnight shifts are supported by adding the end time to the following day when required.

## Security / access roles

The application now includes role-aware access middleware. Administrative users can manage users and assign roles such as Admin, HR, Security, Warehouse Manager, Accountant, Viewer, Employee and Worker. Public registration never accepts a role from the browser; new registrations receive the default `user` role.

## Important

The supplied project archive intentionally does not include the `vendor/` directory. Run `composer install` once on the target Windows/Laragon machine. This avoids shipping a machine-specific PHP dependency tree.

## New Pro Modules (September 2026)

### Multi-language UI
The application now includes a locale system for Arabic, English, French, Spanish, German, Turkish, and Simplified Chinese. The language selector is available in the authenticated top bar and login screen. The layout automatically switches between RTL Arabic and LTR languages. Existing legacy screens that still contain hard-coded Arabic labels can be progressively migrated to translation keys without changing their business logic.

### Payment verification
A new payment workflow supports local and global payment accounts. Administrators can configure payment methods/accounts, users can submit a payment with amount, currency, transaction reference, payer details, and a receipt image/PDF, and administrators can approve or reject the payment with review notes. Uploaded receipts are stored on the server and are served through authenticated routes.

After installing, run `php artisan migrate:fresh --seed` to create the new payment tables and demo payment methods. Replace the demo account values from the Payment Methods screen with the real accounts before production use.

**Important:** This module is a manual electronic-payment verification workflow. It does not claim to process money directly through PayPal, Stripe, banks, or mobile-wallet APIs. Real gateway processing requires the relevant provider credentials/API integration.
#   A l a z z a z i _ i n v o i c e S h e l f  
 