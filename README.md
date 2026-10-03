# 🏢 Laraventry Pro — Inventory & Facility Management

> A full-stack **Laravel 13** web application that combines stock/inventory management with facility operations (people, shifts, attendance, visitors, security) and an electronic payment verification workflow — fully localized in **8 languages** with RTL support.

---

## Table of Contents

1. [Overview](#-overview)
2. [Features](#-features)
3. [Tech Stack](#-tech-stack)
4. [Installation](#-installation)
5. [Running the App](#-running-the-app)
6. [Running Tests](#-running-tests)
7. [Default Credentials](#-default-credentials)
8. [User Roles & Access](#-user-roles--access)
9. [Stock Movement Rules](#-stock-movement-rules)
10. [Payment Verification](#-payment-verification)
11. [Multi-language & RTL](#-multi-language--rtl)
12. [Project Structure](#-project-structure)
13. [Deployment (Production)](#-deployment-production)
14. [Troubleshooting](#-troubleshooting)
15. [License](#-license)

---

## 🔍 Overview

| Component    | Details                                          |
| ------------ | ------------------------------------------------ |
| Framework    | Laravel 13 (PHP 8.3+)                            |
| Database     | SQLite (dev) / MySQL 8+ or PostgreSQL (prod)     |
| Auth         | Session-based with encrypted cookie sessions     |
| Testing      | PHPUnit 12 — 47 tests, 105 assertions (all green)|
| Frontend     | Blade + Bootstrap 5.3 (CDN) + Vite/Tailwind build|
| Localization | AR (default), EN, FR, ES, DE, TR, ZH_CN, NL_BE   |

The system tracks stock per product per warehouse with an immutable audit trail, while also serving as a business-operation platform for facilities: company/mall/property management, staff records, shift scheduling, attendance with automatic hour calculation, visitor registration, entry/exit logs, and manual payment verification with receipt uploads.

---

## ✨ Features

### Inventory Core
- **Products** — CRUD with SKU, purchase/selling prices, units, minimum stock, low-stock filtering, per-warehouse stock view, and 20 latest movements on the detail page
- **Categories** — auto-slug, search, delete protection when in use
- **Suppliers** — multi-column search (name, contact, email, phone), delete protection
- **Warehouses** — unique codes, search, delete protection when stock/history exists
- **Stock Movements** — immutable ledger with `stock_before` / `stock_after` snapshots, filterable by product, warehouse, type, and date range

### Facility Management
- **Facilities** — companies, malls, commercial stores, properties
- **People** — employees, workers, guards, managers, technicians, contractors, tenants…
- **Departments & Work locations**
- **Shifts** — start/end times, grace period, break time, overnight support
- **Attendance** — present / late / absent / leave / half-day / holiday with automatic calculation of worked minutes, late minutes, early departure, and overtime (overnight shifts handled)
- **Visitors** — registration with visitor numbers and host tracking
- **Access Logs** — unified entry/exit log for people and visitors with gates and recorder

### Administration
- **Users & Roles** — admin panel to assign roles, facilities, and activation status
- **Payment Verification** — users submit payments (amount, currency, reference, payer details, receipt upload) and admins approve/reject with notes
- **Payment Methods** — configurable local/global accounts with instructions

### Dashboard
- Inventory value, cost, sales value, expected profit
- Latest stock movements & inventory alerts (low stock)
- Active people, security guards, present/absent today, overtime hours
- 7-day activity chart

---

## 🛠 Tech Stack

```
Backend:   Laravel 13, PHP 8.3+
Database:  SQLite (dev) / MySQL 8+ / PostgreSQL 14+ (prod)
Auth:      Laravel session auth + custom role middleware
Testing:   PHPUnit 12 (+ Pint for code style)
Frontend:  Blade templates, Bootstrap 5.3 (CDN), Bootstrap Icons
Build:     Vite 8 + Tailwind CSS 4 (laravel-vite-plugin)
```

---

## 📦 Installation

### Prerequisites
- PHP 8.3+ with `pdo_sqlite` (or `pdo_mysql`), `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `bcmath`
- Composer 2.x
- Node.js 20+ & npm

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/invoiceShelf-Company/invoiceShelf.git laraventry
cd laraventry

# 2. Install PHP dependencies
composer install

# 3. Create the environment file
cp .env.example .env
php artisan key:generate

# 4. Create the SQLite database (default dev configuration)
touch database/database.sqlite

# 5. Migrate and seed demo data
php artisan migrate:fresh --seed

# 6. Install Node dependencies and build assets
npm install
npm run build
```

> **Tip — one-liner setup:** `composer setup` runs install + key generation + migrations + npm build.

> To use **MySQL** instead, edit `.env`: set `DB_CONNECTION=mysql` and remove the commented `DB_HOST/PORT/DATABASE/USERNAME/PASSWORD` lines below it.

---

## 🚀 Running the App

```bash
# Development server (http://127.0.0.1:8000)
php artisan serve

# Or run everything (server + queue + logs + vite) at once
composer dev
```

**Login with the seeded admin account:**

| Field    | Value                   |
| -------- | ----------------------- |
| Email    | `admin@inventori.test`  |
| Password | `password`              |

---

## 🧪 Running Tests

```bash
php artisan test          # 47 tests, 105 assertions
./vendor/bin/pint --test  # code style (Pint)
./vendor/bin/pint         # auto-fix code style
```

Tests run against an in-memory SQLite database and never touch your dev data.

---

## 🔐 User Roles & Access

Roles are enforced by the `role:` middleware (`App\Http\Middleware\EnsureUserRole`).

| Role                | Access                                                        |
| ------------------- | ------------------------------------------------------------- |
| `super_admin`       | Everything                                                    |
| `admin`             | Everything (all modules, users, payments, inventory)          |
| `hr`                | Facilities, people, shifts, attendance, visitors, payments    |
| `security`          | Visitors, access logs                                         |
| `warehouse_manager` | Inventory: products, categories, suppliers, warehouses, stock |
| `accountant`        | (reserved)                                                    |
| `employee` / `worker` / `viewer` / `user` | Dashboard only                          |

Public registration always creates a plain `user`; role escalation only happens through the admin Users panel.

---

## 📊 Stock Movement Rules

| Type         | Effect                                            |
| ------------ | ------------------------------------------------- |
| `in`         | Adds stock to the selected warehouse              |
| `out`        | Subtracts stock — **blocked if insufficient**     |
| `adjustment` | Corrects stock during stocktaking                 |

- Movements are **immutable** — no edit/delete; corrections are new movements
- `stock_before` and `stock_after` are stored on every record (full audit trail)
- `DB::transaction` + `lockForUpdate` protect against race conditions
- Quick reference: units — `pcs`, `rim`, `sak`, `kg`, `botol`, `box`… (free-form)

---

## 💳 Payment Verification

This module is a **manual electronic-payment verification workflow**:

1. The user picks a payment method (local or global account), enters amount, currency, transaction reference, payer details, and uploads a receipt (image/PDF, max 5 MB)
2. The payment is stored as `pending`
3. An admin opens the payment detail, downloads/inspects the receipt, then **Approves** or **Rejects** with review notes

> ⚠️ It does **not** process money through gateways (PayPal, Stripe, banks). Replace the demo payment-method accounts with real ones via the Payment Methods screen before real use.

---

## 🌍 Multi-language & RTL

- Selector in the top bar and on the login screen: `AR · EN · FR · ES · DE · TR · ZH_CN · NL_BE`
- Arabic is the default; the layout auto-switches **RTL ↔ LTR** per language
- Translations live in `resources/lang/*.json`; the app pre-compiles Arabic literals in Blade views and translates them at render time via `App\Support\LocalizedText`
- Switch URL: `/language/{locale}`

---

## 🏗 Project Structure

```
app/
├── Enums/UserRole.php            # Role enum + labels
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                 # Login, register, logout
│   │   ├── Management/           # Facility, person, shift, attendance,
│   │   │                         # department, visitor, access-log, payment…
│   │   └── ...                   # Dashboard, product, category, supplier…
│   ├── Middleware/
│   │   ├── EnsureUserRole.php    # role:... gate
│   │   └── SetLocale.php         # per-session locale + runtime translation
│   └── Requests/                 # Form Request validation
├── Models/                       # User, Product, StockMovement, Person…
├── Services/Inventory/
│   └── StockMovementService.php  # Transactional stock logic
└── Support/LocalizedText.php     # Localization helper

resources/
├── views/                        # Blade: dashboard, inventory/*, facility/*
└── lang/{ar,en,fr,es,de,tr,zh_CN,nl_BE}.json

database/
├── migrations/                   # ~20 migrations (auth → payments)
├── factories/                    # User, Category, Supplier, Warehouse, Product
└── seeders/DatabaseSeeder.php    # Full demo dataset

routes/web.php                    # Guest routes + role-protected groups
tests/Feature/                    # Auth, Category, Product, StockMovement
```

---

## 🚄 Deployment (Production)

```bash
# 1. Install dependencies without dev packages
composer install --no-dev --optimize-autoloader

# 2. Configure production .env, then:
php artisan key:generate

# 3. Build assets
npm ci && npm run build

# 4. Migrate (never seed in production)
php artisan migrate --force

# 5. Create the first admin via tinker
php artisan tinker
>>> \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@yourdomain.com',
>>>     'password' => bcrypt('STRONG-PASSWORD')])->forceFill(['role' => 'super_admin'])->save();

# 6. Optimize
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 7. Permissions
chmod -R 775 storage bootstrap/cache
```

**Production `.env` checklist:**

```dotenv
APP_ENV=production
APP_DEBUG=false           # REQUIRED
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laraventry
DB_USERNAME=laraventry_user
DB_PASSWORD=STRONG-PASSWORD

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true    # when using HTTPS

LOG_CHANNEL=daily
LOG_LEVEL=warning
```

Point the web root at `public/` and block all dotfiles except `.well-known`.

---

## 🔧 Troubleshooting

| Problem | Fix |
| ------- | --- |
| `Please provide a valid cache path` | Create the storage dirs: `mkdir -p storage/framework/{views,cache/data,sessions} storage/logs bootstrap/cache` |
| `SQLSTATE[HY000]: no such table: sessions` | Run `php artisan migrate` (session driver writes to the DB) |
| `APP_KEY` missing error | `php artisan key:generate` |
| Login fails with correct credentials | `php artisan migrate:fresh --seed` then retry; also `config:clear` |
| Stuck in rate limit (5 logins/min) | Wait 1 minute |
| Stock `out` fails despite enough total stock | Stock is counted **per warehouse**, not globally |
| 403 on pages | Your account's role lacks access — assign a proper role via the Users screen as an admin |
| CSS missing / no styles | `npm install && npm run build` (or `npm run dev`) |
| Consumer view not updating after code changes | `php artisan view:clear && php artisan config:clear` |

---

## 📄 License

MIT License — free to use for personal, educational, and commercial purposes.
