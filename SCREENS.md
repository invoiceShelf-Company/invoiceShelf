# 📱 Laraventry Pro — Screen & Feature Specification

> **Handover document for the UX team.**
>
> This file describes **every screen** of the Laraventry Pro web application (a Laravel/Blade server-rendered app) in detail: what each screen shows, every field, every action, validation rules, permission gates, and states.
>
> **Goal:** build a **mobile UI (Flutter)** covering the same features, backed by the same business logic (a JSON API layer will be added to the Laravel backend for the mobile app — an endpoint map is included in [Appendix A](#appendix-a--endpoint-map-for-the-flutter-app)).

---

## Table of Contents

1. [Global App Conventions](#-global-app-conventions)
2. [Access Matrix (per screen & role)](#-access-matrix-per-screen--role)
3. [Value Lists & Enums](#-value-lists--enums)
4. [Screen Catalog](#-screen-catalog)
   - [A. Authentication — Login, Register](#a-authentication)
   - [B. Dashboard](#b-dashboard)
   - [C. Facilities](#c-facilities)
   - [D. People](#d-people)
   - [E. Departments](#e-departments)
   - [F. Shifts](#f-shifts)
   - [G. Attendance](#g-attendance)
   - [H. Visitors](#h-visitors)
   - [I. Access Logs (Entry/Exit)](#i-access-logs-entryexit)
   - [J. Users & Permissions](#j-users--permissions)
   - [K. Payments](#k-payments)
   - [L. Payment Methods](#l-payment-methods)
   - [M. Products](#m-products)
   - [N. Categories](#n-categories)
   - [O. Suppliers](#o-suppliers)
   - [P. Warehouses](#p-warehouses)
   - [Q. Stock Movements](#q-stock-movements)
5. [Appendix A — Endpoint Map for the Flutter App](#appendix-a--endpoint-map-for-the-flutter-app)
6. [Appendix B — Data Model Cheat Sheet](#appendix-b--data-model-cheat-sheet)

---

## 🌐 Global App Conventions

These behaviors apply to every screen and must be reflected in the mobile design:

| Convention | Web behavior | Notes for mobile |
| ---------- | ------------ | ---------------- |
| **Navigation** | Left sidebar with 5 sections: *Main, Attendance & Security, Administration (admin only), Inventory* | Mobile: drawer nav or bottom tabs; *Administration* section is only visible to admins |
| **Language** | Selector in the top bar; 8 locales: AR (default, RTL), EN, FR, ES, DE, TR, ZH_CN, NL_BE | Mobile: selector on login screen + in profile menu; persist per session; **support RTL layout mirroring for Arabic** |
| **Session** | Cookie session auth; "Remember me" keeps a user logged in | Mobile: token/session storage with auto-login; logout invalidates the session |
| **Flash messages** | Green success / red error banner at top of every page (one per navigation) | Mobile: snackbars/toasts for CRUD results |
| **Field errors** | Validation errors re-render the form, each field highlighted with a red message under it | Mobile: inline field errors + banner summary |
| **Empty states** | Tables/cards show a centered muted message ("No records found.") | Mobile: dedicated empty-state widgets |
| **Pagination** | 10–20 records per page with page links (`withQueryString` filters preserved) | Mobile: infinite scroll is acceptable; keep filter state while scrolling |
| **Delete protection** | Records in use (e.g., category with products) **cannot be deleted** — no delete button/action is exposed at all | Mobile: same — do not offer delete for protected records |
| **403** | Blocked screens abort with *"You do not have permission to access this page."* | Mobile: show a "No access" screen; menu entries for blocked modules should be hidden per role |
| **CSRF** | Every web form carries a CSRF token | Mobile API: use token-based auth instead (see Appendix A) |
| **Timestamps shown** | Dates as `Y-m-d` or `Y/m/d`; times as `HH:mm`; datetime as `Y-m-d H:i` | Mobile: format per device locale, keep 24-hour time |

---

## 🔐 Access Matrix (per screen & role)

Roles enforced by the backend (`role:` middleware). **Admins (`admin`/`super_admin`) always pass.**

| Screen / Module | admin | hr | security | warehouse_manager | user/others |
| --------------- | ----- | -- | -------- | ----------------- | ----------- |
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ |
| Facilities (CRUD) | ✅ | ✅ | ❌ | ❌ | ❌ |
| People (CRUD) | ✅ | ✅ | ❌ | ❌ | ❌ |
| Departments | ✅ | ✅ | ❌ | ❌ | ❌ |
| Shifts | ✅ | ✅ | ❌ | ❌ | ❌ |
| Attendance | ✅ | ✅ | ❌ | ❌ | ❌ |
| Visitors | ✅ | ✅ | ✅ | ❌ | ❌ |
| Access Logs | ✅ | ❌ | ✅ | ❌ | ❌ |
| Users & Permissions | ✅ | ❌ | ❌ | ❌ | ❌ |
| Payments (submit/view) | ✅ | ✅ | ❌ | ✅ | ❌ |
| Payment review (approve/reject) | ✅ | ❌ | ❌ | ❌ | ❌ |
| Payment Methods | ✅ | ❌ | ❌ | ❌ | ❌ |
| Products / Categories / Suppliers / Warehouses / Stock Movements | ✅ | ❌ | ❌ | ✅ | ❌ |

> UX guidance: build each module as a self-contained flow; the app shell should render navigation according to the matrix above.

---

## 📋 Value Lists & Enums

Reuse these exact values (they are stored codes; labels shown on screen):

- **Person types:** `employee` (Employee), `worker` (Worker), `security_guard` (Security guard), `manager`, `supervisor`, `accountant`, `driver`, `technician`, `cleaner`, `sales`, `hr`, `contractor`, `tenant`
- **Person status:** `active`, `on_leave`, `suspended`, `terminated`
- **Facility types:** `company`, `mall`, `store`, `property`, `other`
- **Attendance status:** `present`, `late`, `absent`, `leave`, `half_day`, `holiday`
- **Stock movement types:** `in` (+, receiving goods/returns), `out` (−, sales/shipment/internal use), `adjustment` (stocktaking correction)
- **Payment status:** `pending` → `approved` | `rejected`
- **Payment method scope:** `local` | `global`
- **User roles:** `super_admin`, `admin`, `hr`, `employee`, `worker`, `security`, `warehouse_manager`, `accountant`, `viewer`, `user`
- **Access log action:** `entry` | `exit`

---

# 🖥 Screen Catalog

## A. Authentication

### A1. Login
- **URL:** `GET /login` · **Access:** guests only (authenticated users are redirected to the Dashboard)
- **Purpose:** log into the system with rate limiting (5 attempts/minute per email+IP)

| Element | Details |
| ------- | ------- |
| Brand block | Logo mark + "Laraventry" title + slogan |
| Email field | Required, valid email |
| Password field | Required; typing reveals standard password behavior |
| Remember me | Checkbox → persistent session |
| Submit | "Log in" → Dashboard |
| Footer link | "Don't have an account?" → Register |
| Error state | Wrong credentials → error on the email field: *"The email or password is incorrect."* |
| Rate-limit state | 5 attempts → throttled; message advises waiting |
| Language selector | Top-right dropdown of all 8 languages |

### A2. Register
- **URL:** `GET /register`, `POST /register` · **Access:** guests only
- **Purpose:** self-registration. New users get role `user` (dashboard-only) automatically

| Field | Type | Required | Rules |
| ----- | ---- | -------- | ----- |
| Full name | Text | ✅ | min 2, max 100 |
| Email | Email | ✅ | valid, unique (`users.email`) |
| Password | Password | ✅ | min 8 chars, **mixed case + at least 1 number**, confirmed |
| Confirm password | Password | ✅ | must match |
| Terms | Checkbox | ✅ | must be accepted (`terms.accepted`) |
| Extras | Live password-strength meter (Weak/Medium/Good/Strong, criteria checklist), show/hide password toggles, "passwords match" indicator | | |

On success: auto-login → Dashboard with success toast. **Any error shows in Arabic or the active locale.**

---

## B. Dashboard

- **URL:** `GET /` · **Access:** everyone (any role)
- **Purpose:** one overview screen for inventory + facility operations. Entry screen after login.

**Content:**

1. **Header** — "Welcome, *name* 👋"; sub-line: the user's facility name + type (or a generic slogan). Two quick actions: **Add person** and **Record attendance** (admin/HR only).
2. **8 KPI cards** (tappable, each navigates to its module):

| KPI | Value format | Navigates to |
| --- | ------------ | ------------ |
| Products | count | Products |
| Active people | count | People |
| Security guards | count | People (filtered to guards) |
| Present today | count | Attendance |
| Absent today | count | Attendance (filter absent) |
| Overtime hours | `hh:mm` | Attendance |
| Inventory value | currency 2-dec | Products |
| Visitors | count | Visitors |

3. **7-day line chart** ("Inventory and attendance activity — Live badge"): 3 series — Incoming (`in` movements), Outgoing (`out`), Attendance count. Y starts at 0, legend at bottom.
4. **Financial summary card** — Inventory cost, Sales value, Expected profit (highlighted green), with a progress bar showing cost vs. retail.
5. **Latest stock movements table** (7–10 rows) — columns: Product (name + SKU), Warehouse, Type badge (In/Out/Adjustment), Quantity (bold), Date. Action: "View all" → Stock movements history. Empty: "No movements."
6. **Inventory alerts card** — low-stock products list: name + SKU + current total in red. Header badge with count. Empty: green shield "Inventory is in good condition." Each row → Product detail.

> **Mobile hint:** KPIs as a 2-column grid; chart full-width; movements and alerts as vertical card lists. Quick actions in a FAB or header row.

---

## C. Facilities

### C1. Facilities list
- **URL:** `GET /facilities` · **Access:** admin, hr
- **Layout:** card grid (2–3 columns desktop) with pagination

**Each card:** type badge (Company/Mall/…), active status badge (Active/Stopped), facility **name** (large), **code**, people count (👥 n), two actions: **Details** → C3, **Edit** → C2.

**Empty state:** "No facilities yet." · **Action:** "Add facility" → C2.

### C2. Facility create / edit
- **URLs:** `GET /facilities/create`, `GET /facilities/{id}/edit` · **Access:** admin, hr
- **Form (single page):**

| Field | Type | Required | Rules |
| ----- | ---- | -------- | ----- |
| Establishment name | Text | ✅ | max 255 |
| Code | Text | ✅ | max 50, **unique** across facilities |
| Place type | Select | ✅ | company / mall / store / property / other |
| Phone | Tel | — | |
| Email | Email | — | valid format if filled |
| Address | Text | — | |
| Description | Multiline (4 rows) | — | |
| Establishment active | Switch | — | default on |

### C3. Facility detail
- **URL:** `GET /facilities/{id}` · **Access:** admin, hr

**Content:** header (type eyebrow + name + description) with Edit button; **3 stat cards**: People count, Visitors count, Shifts count; **two lists**: its **Departments** (name + code) and its **Shifts** (name + `start → end`), each with an empty state.

> **Mobile hint:** Module = 3 screens (grid → form → tabs or stacked detail). Reuse the same form component for create/edit.

---

## D. People

### D1. People list
- **URL:** `GET /people` · **Access:** admin, hr
- **Purpose:** master directory of every person in the company/property

**Filter bar (applies via query string, sticky over pagination):**

| Filter | Type | Notes |
| ------ | ---- | ----- |
| Search `q` | Text | matches name or employee number |
| Type | Select | all 13 person types |
| Status | Select | Active / On leave / Suspended |

**Table columns:** Person (**full name** + employee number below, "No number" if empty) · Type badge · Facility · Department ("—" if none) · Shift ("—" if none) · Status badge (green/warning/gray by `active`/`on_leave`/`suspended`/`terminated`) · **View** → D3.

**Actions:** "Add person" → D2. **Empty:** "No records." · Paginated.

### D2. Person create / edit (long form)
- **URLs:** `GET /people/create`, `GET /people/{id}/edit` · **Access:** admin, hr
- **Header:** "Add new person" / "Edit person data"

**Fields:**

| Field | Type | Required | Rules / notes |
| ----- | ---- | -------- | ------------- |
| Full name | Text | ✅ | |
| Employee number | Text | — | shown on cards/lists if set |
| Person type | Select | ✅ | 13 types (see value lists) |
| Facility | Select | ✅ | options: `name — type` |
| Department | Select | — | "No department" option |
| Shift | Select | — | "No shift" option; option label: `name (HH:MM - HH:MM)` |
| Job title | Text | — | |
| Phone | Tel | — | |
| Email | Email | — | |
| ID number | Text | — | |
| Birth date | Date | — | |
| Join date | Date | — | |
| Status | Select | — | active / on_leave / suspended / terminated |
| Work location | Text | — | |
| Floor | Text | — | |
| Room / Unit | Text | — | |
| **Info banner** | — | — | *"Security fields below are for security guards; can be left empty for others."* |
| Security company | Text | — | |
| Security permit number | Text | — | |
| Permit expiry | Date | — | |
| Guard post | Text | — | |
| Notes | Multiline (4) | — | |

> **Mobile hint:** group into collapsible sections: *Basics → Workspace (facility/department/shift/status/locations) → Contact → Security fields (only expanded when `security_guard` type is picked) → Notes*. Dropdown of shifts/facilities is large → use native pickers.

### D3. Person detail
- **URL:** `GET /people/{id}` · **Access:** admin, hr

**Content:** header (type eyebrow, **full name** as title, subtitle: employee number "No employee number" if empty + facility) with **Edit** button; **4 info cards**: Facility, Department, Shift, Status; **Work details panel** (label/value list): Job title, Phone, Work location, Guard post, Security permit, Permit expiry (each "—" when empty); **Latest attendance table**: Date, Check-in, Check-out, Worked (`h:mm`), Overtime (green) + **All records** action → Attendance screen pre-filtered to this person. Empty: "No attendance recorded."

> **Mobile hint:** person list → detail = master-detail pattern; the recent attendance list could be a simple vertical timeline.

---

## E. Departments

### E1. Departments list
- **URL:** `GET /departments` · **Access:** admin, hr
- **Table:** Department (**name** + description below) · Code ("—") · Facility · People count · button **Edit**. **Actions:** "Add department". **Empty:** "No departments."

### E2. Department add / edit
- **URLs:** `GET /departments/create`, `GET /departments/{id}/edit`
- **Fields:** Facility ✅ (select) · Name ✅ (max 255) · Code (max 50, nullable) · Description (multiline)

> **Mobile hint:** this is a light module — single list + simple full-screen form.

---

## F. Shifts

### F1. Shifts list
- **URL:** `GET /shifts` · **Access:** admin, hr
- **Table:** Shift (**name**, blue **"Night"** badge when `is_overnight`) · Facility · Hours (`HH:MM → HH:MM`) · Grace period (`n minutes`) · Break (`n minutes`) · People count · **Edit**. **Actions:** "Add shift". **Empty:** "No shifts."

### F2. Shift add / edit
- **URLs:** `GET /shifts/create`, `GET /shifts/{id}/edit`

| Field | Type | Required | Rules |
| ----- | ---- | -------- | ----- |
| Facility | Select | ✅ | |
| Shift name | Text | ✅ | max 100 |
| Start time | Time picker | ✅ | |
| End time | Time picker | ✅ | |
| Grace period | Number (min 0) | — | minutes; default 10 |
| Break (minutes) | Number (min 0) | — | default 0 |
| Overnight shift extends to next day | Switch | — | adds end-time to the following day for calculations |

> The backend **auto-calculates** from the chosen shift: worked minutes, late minutes, early departure, overtime.

---

## G. Attendance

### G1. Attendance day view
- **URL:** `GET /attendance?date=YYYY-MM-DD&status=&person_id=` · **Access:** admin, hr
- **Purpose:** per-day records board with automatic calculations

**Content:**
1. Header shows the selected date ("Attendance & Overtime") + **Record attendance** action.
2. **5 day-stat cards:** Present, Late, Absent, On leave, Overtime (`hh:mm`).
3. **Filter bar:** Date (date picker) + Status (all/present/late/absent/leave) + Show button.
4. **Records table:** Person (**name** + type label below) · Scheduled hours (`HH:MM → HH:MM`, "—" if the person has no shift) · Check-in ("—") · Check-out ("—") · Worked hours · Late (`hh:mm`, orange) · Overtime (green, bold) · Status badge · **Edit** button.

**Empty for the day:** "No records for this day." · Paginated.

### G2. Attendance record / edit
- **URLs:** `GET /attendance/create`, `GET /attendance/{id}/edit` · **Access:** admin, hr

| Field | Type | Required | Notes |
| ----- | ---- | -------- | ----- |
| Person | Select | ✅ | label: `name — type — shift` (shift only when set) |
| Date | Date picker | ✅ | default today |
| Status | Select | ✅ | six statuses; default `present` |
| Check-in time | Time picker | — | optional |
| Check-out time | Time picker | — | optional |
| Info banner | — | — | "System auto-calculates worked/late/early-departure/overtime based on the shift" |
| Notes | Multiline (4) | — | |

**Primary button:** "Save and calculate hours". On save the app computes worked minutes, lateness beyond grace, early departure, and overtime (overnight shifts end on the next day).

---

## H. Visitors

### H1. Visitors list
- **URL:** `GET /visitors` · **Access:** admin, hr, security
- **Header:** "Visitors" + slogan + **Register visitor** action.
- **Table:** Visitor (**name** + company below) · Number (`VIS-…` auto-generated) · Facility · Host ("—") · Purpose ("—") · Phone ("—").
- **Empty:** "No visitors." · Paginated (15/page).

### H2. Visitor registration form
- **URL:** `GET /visitors/create` · **Access:** admin, hr, security

| Field | Type | Required |
| ----- | ---- | -------- |
| Facility | Select | ✅ |
| Visitor name | Text | ✅ |
| Phone | Tel | — |
| ID (national) | Text | — |
| Company | Text | — |
| Host person | Text | — |
| Purpose of visit | Text | — |
| Notes | Multiline | — |

Visitor number is **auto-generated server-side** (`VIS-yymmddHHMMss-XXXX`) — the form has no number field.

> **Mobile hint:** the "Register visitor" flow is the security-guard's daily screen; make it one-step from the bottom bar for `[security]` role users; the generated number should be shown in the success toast and on the list rows.

---

## I. Access Logs (Entry/Exit)

### I1. Access log list
- **URL:** `GET /access-logs` · **Access:** admin, security
- **Purpose:** unified log of entries/exits for people and visitors per gate
- **Table:** Person/Visitor (**full name** or visitor name; sub-line: person type or visitor number) · Facility · Movement badge (green **Entry** / red **Exit**) · Gate ("—") · Time (`Y-m-d HH:i`) · Recorder ("—").
- **Header action:** "Record movement" → I2. **Empty:** "No movements."

### I2. Record movement form
- **URL:** `GET /access-logs/create` · **Access:** admin, security

| Field | Type | Required | Notes |
| ----- | ---- | -------- | ----- |
| Facility | Select | ✅ | |
| Person | Select | — | "— Visitor —" placeholder; label `name — type` |
| Visitor | Select | — | "Without" placeholder; label `name — VIS-number` |
| Movement | Select | ✅ | `entry` / `exit` (badges green/red) |
| Gate | Text | — | |
| Date and time | DateTime picker | ✅ | defaults to now (`YYYY-MM-DDTHH:mm`) |
| Notes | Multiline | — | |

At least one of Person/Visitor must be selected (`required_with` semantics on the backend).

---

## J. Users & Permissions

### J1. Users list
- **URL:** `GET /users` · **Access:** admin only
- **Purpose:** manage system access of app users
- **Table:** User (**name** + email below) · Role badge (10 roles) · Facility ("All establishments" if none) · Status badge (Active green / Suspended red) · Last login (`Y-m-d H:i` or "—") · **Manage** → J2. **Empty:** "No users." · 20/page.

### J2. User edit ("user management")
- **URL:** `GET /users/{id}/edit`, `PUT` · **Access:** admin only
- Slogan: "Set the user's role and the establishment they work at."

| Field | Type | Required | Notes |
| ----- | ---- | -------- | ----- |
| Name | Text | ✅ | |
| Email | Email | ✅ | unique, ignoring self |
| Role | Select | ✅ | 10 roles, Arabic labels on web |
| Facility | Select | — | "All establishments" placeholder |
| New password | Password | — | min 8 |
| Confirm password | Password | — | must match when provided |
| Account active | Switch | — | value 0/1 |

Success toast: "User and permissions updated." Inactive users cannot log in (login error).

---

## K. Payments

### K1. Payment history
- **URL:** `GET /payments` · **Access:** admin, hr, warehouse_manager
- **Header:** "Payments & Verification" eyebrow + "Payment History" + **Submit Payment** action.
- **Table:** Payment ID · Method name · Amount (2-dec) · Reference ("—") · Submitted by · Status badge (**Pending** yellow / **Approved** green / **Rejected** red) · Date · **Details** → K3. **Empty:** "No payments found." · 15/page.
- Actions: admin can also see **Payments → Payment Methods** link.

### K2. Submit payment
- **URL:** `GET /payments/create`, `POST` · **Access:** admin, hr, warehouse_manager

| Field | Type | Required | Notes |
| ----- | ---- | -------- | ----- |
| Payment method | Select | ✅ | active methods, label: `name — CURRENCY (Global/Local)` |
| Amount | Number (step 0.01, min 0.01) | ✅ | |
| Currency | Text (max 10) | ✅ | default `USD` |
| Reference | Text (max 255) | — | transaction ID |
| Payer name | Text | — | |
| Payer account | Text | — | |
| Purpose | Text | — | |
| **Receipt** | File upload | ✅ | image (jpg/jpeg/png/webp) or PDF, **max 5 MB** |

Right column lists **available payment accounts** (name, global/local badge, provider, account name/number/currency, instructions) — for the mobile UI keep this as an expandable info sheet per selected method. Success toast: "Payment submitted successfully."

### K3. Payment detail (+ review)
- **URL:** `GET /payments/{id}` · **Access:** admin, hr, warehouse_manager · **Review:** admin only
- **Content, left card:** Amount (large, `1,234.56 CUR`), Status badge, Reference ("—"), **Submitted by** (name), Payer Name ("—"), Payer Account ("—"), Review notes section (only when present).
- **Content, right card:** "Payment Receipt" → **Download receipt** button; if the receipt is an image it renders inline; if PDF → notice text.
- **Admin-only review card** (when `status = pending`): a Review notes textarea and two submit buttons — **Approve** (green, sends `status=approved`) and **Reject** (red, `status=rejected`), sets reviewer + timestamp. Review confirmation toast: "Payment reviewed successfully."

> **Mobile hint:** primary mobile flows are (a) submit payment with camera/photo-library receipt, (b) admin review queue with approve/reject quick actions from the list swipe gestures.

---

## L. Payment Methods

- **URL:** `GET /payment-methods` · **Access:** admin only
- **Single screen containing both the list and the add form.**
- **Left — list table:** Method (**name**) · Provider · Scope (Global/Local badge) · Account (name + number) · Currency · Status (Active/Inactive) · **Update** (inline edit form per row, PUT). Empty: "No payment methods available."
- **Right — "Add Payment Method" form:**

| Field | Type | Required |
| ----- | ---- | -------- |
| Payment method | Text | ✅ |
| Provider | Text | — |
| Scope | Select (Local/Global) | — |
| Currency | Text | default `USD` |
| Account Name | Text | — |
| Account Number | Text | — |
| Instructions | Multiline (3) | — |
| Is active | Switch | default on |

Success toast: "Payment method saved successfully."

---

## M. Products

### M1. Products list
- **URL:** `GET /products` · **Access:** admin, warehouse_manager
- **Filter card:** search by name/SKU · Category select · Supplier select · Stock select (All stock / Low stock) · Filter + Reset buttons.
- **Table columns:** # (row) · **SKU** (mono) · Product (**name** bold) · Category · Supplier · Purchase price · Selling price · **Total stock** (summed across warehouses) · Status badge (Active green / Inactive gray) · Actions: **Detail** · **Edit**.
- **Actions:** "Add product". **Empty:** "No product data." · 10/page.

### M2. Product create / edit
- **URLs:** `GET /products/create`, `GET /products/{id}/edit` · **Access:** admin, warehouse_manager

| Field | Type | Required | Rules |
| ----- | ---- | -------- | ----- |
| Category | Select | ✅ | must exist |
| Supplier | Select | — | "— No supplier —" option |
| Name | Text | ✅ | max 255 |
| SKU | Text | ✅ | max 100, **unique** |
| Description | Multiline | — | max 5000 |
| Unit | Text | — | free-form, e.g., pcs/rim/kg/box |
| Purchase price | Number | — | ≥ 0 |
| Selling price | Number | — | ≥ 0 |
| Minimum stock | Number | — | integer ≥ 0 (default 5); alert trigger |
| Active status | Switch | — | default on |

> Stock is **never entered on this form** — it starts through a stock movement (Q2). Create redirects to the product detail.

### M3. Product detail
- **URL:** `GET /products/{id}` · **Access:** admin, warehouse_manager
- **Content:** header with product name + Edit + Back actions · info grid (category, supplier, unit, prices, min stock, active, creator, dates) · description panel (or "No description") · **Stock per warehouse table**: Warehouse (name + code), Current quantity, Last updated ("No stock yet" empty state) with **Add to new warehouse** action → mini form (pick warehouse + initial quantity) which records an `in` movement · **Latest 20 movements table**: Type badge, Quantity, Before/After, Reference ("—"), Date, By (user). Empty: "No movement history yet."

> **Mobile hint:** detail = segmented tabs: *Info / Stock by warehouse / Movements*.

---

## N. Categories

### N1. Categories list
- **URL:** `GET /categories?search=` · **Access:** admin, warehouse_manager
- **Search field** by name · **Table:** # · **Name** · Slug (mono) · Description · Products count · Status (Active/Inactive badge) · Actions: **Edit** · **Delete** (only when unused). Empty: "No category data." · 10/page, search preserved.

### N2. Category create / edit
- **URLs:** `GET /categories/create`, `GET /categories/{id}/edit`
- **Fields:** Name ✅ (max 255, unique) · Slug (max 255, unique) — auto-generated from the name but editable · Description (multiline) · Active ✅ switch.

### N3. Category delete semantics
- A category **in use by products cannot be deleted** — the server keeps a count on the product card/list; do the same guard on mobile (hide or disable).

*(Suppliers and Warehouses follow the same pattern; see O and P.)*

---

## O. Suppliers

### O1. Suppliers list
- **URL:** `GET /suppliers?search=` · **Access:** admin, warehouse_manager
- **Search by name / contact person / email / phone** (single input).
- **Table:** # · Supplier (**name**) · Contact person · Phone · Email · Products count · Status badge · **Edit** · **Delete** (blocked while products reference the supplier).
- **Fields on create/edit (`GET /suppliers/create`, `GET /suppliers/{id}/edit`):**
  - Name ✅ (max 255) · Contact person (max 100) · Phone (max 30) · Email (valid if provided, max 255) · Address (multiline) · **Is active ✅** switch.

---

## P. Warehouses

### P1. Warehouses list
- **URL:** `GET /warehouses?search=` · **Access:** admin, warehouse_manager
- **Search by name or code**. **Table:** # · **Code** (mono) · Warehouse (**name**) · **Location / Address** · Status · **Edit** · **Delete** (blocked if the warehouse holds stock or has movement history).
- **Fields on create/edit:** Name ✅ (unique) · Code ✅ (unique, format hint `GDG-001`) · Location/Address (text, editable multiline on edit) · **Is active ✅** switch.

> **Mobile hint:** warehouses are small master data — card list (code highlight) + one-step forms.

---

## Q. Stock Movements

### Q1. Movement history / ledger
- **URL:** `GET /stock-movements?product_id=&warehouse_id=&type=&movement_date_from=&movement_date_to=` · **Access:** admin, warehouse_manager
- **Purpose:** immutable ledger of all stock ins/outs/adjustments (audit trail)
- **Filter bar:** Product select (label `name (SKU)`) · Warehouse select · Type select (All types / In / Out / Adjustment) · Date from · Date to · Filter button.
- **Table:** Date (`Y/m/d`) · Movement-time · Product (**name** + SKU) · Warehouse · Type badge (green **In (+)** / red **Out (−)** / yellow **Adjustment**) · Quantity (bold) · Before → After · Reference ("—") by number · Notes ("—") · By (creator user).
- **Header action:** "Record new movement" → Q2. **Empty:** "No stock movements." · 15/page.

### Q2. Record stock movement
- **URL:** `GET /stock-movements/create`, `POST` · **Access:** admin, warehouse_manager

| Field | Type | Required | Rules |
| ----- | ---- | -------- | ----- |
| Product | Searchable select | ✅ | exists in `products` |
| Warehouse | Select | ✅ | exists in `warehouses` |
| Movement type | Segmented control | ✅ | `in` (+) / `out` (−) / `adjustment` |
| Quantity | Number (min 1, integer) | ✅ | min 1 |
| Movement date | Date picker | ✅ | default today |
| Reference number | Text (max 100) | — | e.g., PO-001, INV-2023-001 |
| Notes | Multiline (3) | — | max 5000 |

- **Info banner:** *"Recorded stock movements cannot be edited or deleted to protect data integrity."*
- **Guide card** (contextual help): meaning of each type — `in`=receiving goods or returns, `out`=sales/shipment/internal use, `adjustment`=stocktaking correction (enter a positive quantity; the system applies it according to the type).
- **Critical business rule for UX:** if `out` quantity exceeds available stock in that warehouse, the save fails with *"Insufficient stock"* on the quantity field — surface early if quantity > current stock of the selected product+warehouse (the product detail's stock-per-warehouse values can pre-load current totals offline-friendly).
- Success returns to the ledger with a flash message.

---

# Appendix A — Endpoint Map for the Flutter App

The current backend is server-rendered (Blade) with **session-cookie** auth — no JSON API yet. To power the mobile app, add a JSON/REST layer (Laravel controllers + Sanctum tokens) exposing the same logic. This is the map the endpoints should cover:

| # | Mobile screen (section above) | Method & path (suggested) | Notes |
| - | ----------------------------- | -------------------------- | ----- |
| Auth | | `POST /api/register`, `POST /api/login`, `POST /api/logout` | Sanctum token auth; `locale` param for all responses |
| Auth | | `GET /api/locales` | list of 8 locales + `dir` (rtl/ltr) |
| Dashboard | B | `GET /api/dashboard` | KPIs, chart series, financial summary, latest movements, alerts |
| Facilities | C | `GET/POST /api/facilities`, `GET/PUT /api/facilities/{id}` | include `people_count`, `visitors_count`, `shifts` on detail |
| People | D | `GET/POST /api/people`, `GET/PUT /api/people/{id}` | filters `q,type,status` |
| Departments | E | `GET/POST /api/departments`, `PUT /api/departments/{id}` | |
| Shifts | F | `GET/POST /api/shifts`, `PUT /api/shifts/{id}` | include `people_count` |
| Attendance | G | `GET /api/attendance?date=&status=&person_id=`, `GET/POST /api/attendance`, `PUT /api/attendance/{id}` | returns calculated breakdown (worked/late/early/overtime) |
| Visitors | H | `GET/POST /api/visitors` | server generates `visitor_number` |
| Access logs | I | `GET/POST /api/access-logs` | `person_id` or `visitor_id` required |
| Users | J | `GET /api/users`, `GET/PUT /api/users/{id}` | admin-only; role list endpoint `GET /api/roles` |
| Payments | K | `GET/POST /api/payments`, `GET /api/payments/{id}`, `POST /api/payments/{id}/review` | multipart upload for receipt; review admin-only |
| Receipt | K | `GET /api/payments/{id}/receipt` | file download/stream |
| Payment methods | L | `GET/POST /api/payment-methods`, `PUT /api/payment-methods/{id}` | admin-only |
| Products | M | `GET/POST /api/products`, `GET/PUT /api/products/{id}` | filters `search,category_id,supplier_id,low_stock`; detail includes stocks + recent movements |
| Add stock to warehouse | M3 | included in Q (movement `in`) or `POST /api/products/{id}/stocks` | |
| Categories | N | `GET/POST /api/categories`, `GET/PUT /api/categories/{id}` | unique name/slug |
| Suppliers | O | `GET/POST /api/suppliers`, `GET/PUT /api/suppliers/{id}` | multi-field search |
| Warehouses | P | `GET/POST /api/warehouses`, `GET/PUT /api/warehouses/{id}` | |
| Stock movements | Q | `GET/POST /api/stock-movements` | read-only after create; 409 on insufficient stock |

Error contract to adopt globally:
- `422` validation → field→message map (same messages as the web app)
- `403` role gate → "No access" screen
- `409`-style business errors (insufficient stock, protected delete) → translated business messages
- All list endpoints accept `page` + their filters and return paginated payloads (`.links()` equivalence).

---

# Appendix B — Data Model Cheat Sheet

Entities the mobile app will bind to (with the key fields surfaced in UI):

| Entity | Displayed fields (main) | Relations used in UI |
| ------ | ----------------------- | -------------------- |
| User | name, email, role, facility, is_active, last_login_at | facility |
| Facility | name, code, type, phone, email, address, description, is_active | people, departments, shifts, visitors |
| Person | full_name, employee_number, person_type, status, facility, department, shift, job_title, phone, email, national_id, birth_date, join_date, work_location, floor, room, security_company, security_permit_number, security_permit_expires_at, guard_post, notes | facility, department, shift, attendances |
| Department | name, code, description, facility | facility, people_count |
| Shift | name, facility, start_time, end_time, grace_minutes, break_minutes, is_overnight | facility, people_count |
| Attendance | person, attendance_date, status, scheduled_start/end, check_in, check_out, worked/late/early/overtime minutes, notes | person, shift-derived schedule |
| Visitor | name, phone, national_id, company, host_name, purpose, visitor_number, facility, notes | facility |
| AccessLog | person/visitor, action (entry/exit), gate, logged_at, recorder, notes | facility, person, visitor, user |
| Payment | method, amount, currency, reference, payer_name/account, purpose, receipt_path, status, submitted_by, reviewed_by/at, review_notes | payment_method, submitter, reviewer, facility |
| PaymentMethod | name, provider, scope, account_name/number, currency, instructions, logo_path, is_active, sort_order | — |
| Product | name, sku, description, unit, purchase_price, selling_price, minimum_stock, is_active, category, supplier, creator | category, supplier, stocks(warehouse), stockMovements |
| ProductStock | product+warehouse (unique), quantity, last updated | product, warehouse |
| StockMovement | product, warehouse, type, quantity, stock_before, stock_after, reference_number, movement_date, notes, creator (immutable) | product, warehouse, user |
| Category | name, slug, description, is_active, products_count | — |
| Supplier | name, contact_name, phone, email, address, is_active, products_count | — |
| Warehouse | name, code, address, is_active | stocks, stockMovements |

---

*End of specification — generated from the live screens of the Laraventry Pro web app. Ask the backend team for the JSON API (Appendix A) before implementation; all business logic and localization already exist there.*
