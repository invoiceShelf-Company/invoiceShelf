# Rencana Project Manajemen Inventori Laravel

Dokumen ini adalah peta kerja untuk membangun aplikasi manajemen inventori sebagai latihan CRUD, relasi database, validasi, transaksi stok, dan struktur kode Laravel yang rapi. Project dimulai dari versi kecil yang bisa selesai, lalu ditambah fitur secara bertahap.

## 1. Tujuan Project

Tujuan utama aplikasi:

- Mengelola data barang.
- Mengelola kategori barang.
- Mengelola supplier.
- Mengelola lokasi penyimpanan atau gudang.
- Mencatat stok masuk dan stok keluar.
- Menampilkan ringkasan stok, barang menipis, dan riwayat pergerakan stok.
- Melatih relasi database Laravel secara nyata.

Kemampuan Laravel yang akan dilatih:

- Migration dan schema design.
- Eloquent model dan relationship.
- Resource controller.
- Form request validation.
- Route grouping dan route model binding.
- Service class untuk business logic.
- Database transaction.
- Seeder dan factory.
- Pagination, search, filter, dan sorting.
- Feature test untuk alur penting.

## 2. Scope Versi Awal

Versi awal sebaiknya tidak terlalu besar. Fokuskan dulu pada fitur yang membentuk inti inventori.

Fitur MVP:

- CRUD kategori.
- CRUD supplier.
- CRUD lokasi/gudang.
- CRUD produk.
- Stok masuk.
- Stok keluar.
- Riwayat pergerakan stok.
- Dashboard ringkas.

Fitur lanjutan setelah MVP stabil:

- Multi-user dengan role admin/staff.
- Export CSV atau Excel.
- Upload gambar produk.
- Barcode/SKU generator.
- Audit log.
- Laporan nilai persediaan.
- Purchase order sederhana.
- Penyesuaian stok atau stock opname.

## 3. Desain Database

Gunakan migration Laravel untuk semua tabel. Hindari mengedit database langsung lewat GUI, supaya struktur database bisa dilacak dan diulang dari awal.

### 3.1 Tabel `users`

Tabel ini sudah dibuat oleh Laravel. Nantinya bisa dipakai untuk mencatat siapa yang membuat produk dan siapa yang melakukan pergerakan stok.

Kolom bawaan:

- `id`
- `name`
- `email`
- `password`
- `remember_token`
- `created_at`
- `updated_at`

Tambahan opsional setelah auth berjalan:

- `role`, enum/string: `admin`, `staff`

Relasi:

- User has many Products melalui `created_by`.
- User has many StockMovements melalui `created_by`.

### 3.2 Tabel `categories`

Dipakai untuk mengelompokkan produk.

Kolom:

- `id`
- `name`, string, wajib, unique.
- `slug`, string, wajib, unique.
- `description`, text, nullable.
- `is_active`, boolean, default true.
- `created_at`
- `updated_at`

Relasi:

- Category has many Products.
- Product belongs to Category.

Catatan implementasi:

- `slug` dibuat dari `name`.
- Saat kategori masih punya produk, sebaiknya jangan langsung dihapus permanen. Untuk awal, cukup cegah delete jika masih dipakai.

### 3.3 Tabel `suppliers`

Dipakai untuk menyimpan vendor/pemasok barang.

Kolom:

- `id`
- `name`, string, wajib.
- `contact_name`, string, nullable.
- `phone`, string, nullable.
- `email`, string, nullable.
- `address`, text, nullable.
- `is_active`, boolean, default true.
- `created_at`
- `updated_at`

Relasi:

- Supplier has many Products.
- Product belongs to Supplier.

Catatan implementasi:

- Supplier bisa nullable di produk jika barang tidak punya pemasok tetap.
- Gunakan validasi email bila field email diisi.

### 3.4 Tabel `warehouses`

Untuk latihan relasi dan kasus inventori yang lebih realistis, gunakan gudang/lokasi penyimpanan. Jika ingin lebih sederhana, nama tabel bisa `locations`, tetapi `warehouses` lebih jelas untuk domain inventori.

Kolom:

- `id`
- `name`, string, wajib, unique.
- `code`, string, wajib, unique.
- `address`, text, nullable.
- `is_active`, boolean, default true.
- `created_at`
- `updated_at`

Relasi:

- Warehouse has many ProductStocks.
- Warehouse has many StockMovements.

### 3.5 Tabel `products`

Tabel inti untuk data barang.

Kolom:

- `id`
- `category_id`, foreign id, constrained ke `categories`.
- `supplier_id`, foreign id nullable, constrained ke `suppliers`.
- `created_by`, foreign id nullable, constrained ke `users`.
- `name`, string, wajib.
- `sku`, string, wajib, unique.
- `description`, text, nullable.
- `unit`, string, default `pcs`.
- `purchase_price`, decimal 15,2, default 0.
- `selling_price`, decimal 15,2, default 0.
- `minimum_stock`, unsigned integer, default 0.
- `is_active`, boolean, default true.
- `created_at`
- `updated_at`

Relasi:

- Product belongs to Category.
- Product belongs to Supplier.
- Product belongs to User sebagai creator.
- Product has many ProductStocks.
- Product has many StockMovements.

Catatan implementasi:

- Jangan simpan stok utama hanya di tabel `products` jika memakai banyak gudang. Simpan stok per produk per gudang di `product_stocks`.
- Jika MVP ingin sangat sederhana, boleh mulai dengan `current_stock` di `products`, tetapi rencana yang lebih matang adalah memakai `product_stocks`.

### 3.6 Tabel `product_stocks`

Tabel ini menyimpan jumlah stok saat ini per produk dan per gudang.

Kolom:

- `id`
- `product_id`, foreign id, constrained ke `products`.
- `warehouse_id`, foreign id, constrained ke `warehouses`.
- `quantity`, unsigned integer, default 0.
- `created_at`
- `updated_at`

Constraint penting:

- Unique gabungan `product_id` dan `warehouse_id`.

Relasi:

- ProductStock belongs to Product.
- ProductStock belongs to Warehouse.

Alasan tabel ini penting:

- Produk yang sama bisa ada di beberapa gudang.
- Stok saat ini bisa dihitung cepat tanpa menjumlah seluruh riwayat transaksi setiap kali halaman dibuka.

### 3.7 Tabel `stock_movements`

Tabel ini adalah riwayat stok masuk, keluar, dan penyesuaian. Semua perubahan stok harus tercatat di sini.

Kolom:

- `id`
- `product_id`, foreign id, constrained ke `products`.
- `warehouse_id`, foreign id, constrained ke `warehouses`.
- `created_by`, foreign id nullable, constrained ke `users`.
- `type`, string atau enum: `in`, `out`, `adjustment`.
- `quantity`, unsigned integer, wajib.
- `stock_before`, unsigned integer, wajib.
- `stock_after`, unsigned integer, wajib.
- `reference_number`, string, nullable.
- `movement_date`, date, wajib.
- `notes`, text, nullable.
- `created_at`
- `updated_at`

Relasi:

- StockMovement belongs to Product.
- StockMovement belongs to Warehouse.
- StockMovement belongs to User sebagai creator.

Aturan bisnis:

- `in`: stok bertambah.
- `out`: stok berkurang.
- `adjustment`: stok disesuaikan manual untuk koreksi.
- Stok keluar tidak boleh membuat stok menjadi negatif.
- Setiap perubahan stok harus memakai database transaction.
- `stock_before` dan `stock_after` wajib disimpan agar riwayat tetap jelas walaupun stok sekarang berubah.

### 3.8 Tabel Opsional `units`

Untuk awal, `unit` cukup string di tabel `products`. Kalau ingin lebih rapi, buat tabel `units`.

Kolom:

- `id`
- `name`, contoh: `piece`, `box`, `kilogram`.
- `symbol`, contoh: `pcs`, `box`, `kg`.
- `created_at`
- `updated_at`

Relasi:

- Unit has many Products.
- Product belongs to Unit.

Rekomendasi:

- Jangan buat tabel ini di hari pertama. Tambahkan setelah CRUD produk stabil.

## 4. Urutan Migration

Buat migration dengan urutan berikut:

```bash
php artisan make:model Category -mcr
php artisan make:model Supplier -mcr
php artisan make:model Warehouse -mcr
php artisan make:model Product -mcr
php artisan make:model ProductStock -m
php artisan make:model StockMovement -mcr
```

Urutan migrasi yang disarankan:

1. `categories`
2. `suppliers`
3. `warehouses`
4. `products`
5. `product_stocks`
6. `stock_movements`

Foreign key harus mengarah ke tabel yang sudah dibuat lebih dulu.

## 5. Model dan Relasi Eloquent

### 5.1 `Category`

Isi model:

- `$fillable`: `name`, `slug`, `description`, `is_active`.
- Relationship `products()`.

Method relasi:

```php
public function products()
{
    return $this->hasMany(Product::class);
}
```

### 5.2 `Supplier`

Isi model:

- `$fillable`: `name`, `contact_name`, `phone`, `email`, `address`, `is_active`.
- Relationship `products()`.

### 5.3 `Warehouse`

Isi model:

- `$fillable`: `name`, `code`, `address`, `is_active`.
- Relationship `stocks()`.
- Relationship `stockMovements()`.

### 5.4 `Product`

Isi model:

- `$fillable`: `category_id`, `supplier_id`, `created_by`, `name`, `sku`, `description`, `unit`, `purchase_price`, `selling_price`, `minimum_stock`, `is_active`.
- Cast decimal untuk harga jika diperlukan.
- Relationship `category()`.
- Relationship `supplier()`.
- Relationship `creator()`.
- Relationship `stocks()`.
- Relationship `stockMovements()`.

Method tambahan yang berguna:

- `totalStock()`: menjumlah `product_stocks.quantity`.
- `isLowStock()`: membandingkan total stock dengan `minimum_stock`.

### 5.5 `ProductStock`

Isi model:

- `$fillable`: `product_id`, `warehouse_id`, `quantity`.
- Relationship `product()`.
- Relationship `warehouse()`.

### 5.6 `StockMovement`

Isi model:

- `$fillable`: `product_id`, `warehouse_id`, `created_by`, `type`, `quantity`, `stock_before`, `stock_after`, `reference_number`, `movement_date`, `notes`.
- Cast `movement_date` ke date.
- Relationship `product()`.
- Relationship `warehouse()`.
- Relationship `creator()`.

## 6. Struktur Route

Untuk aplikasi web Blade, route utama ada di `routes/web.php`.

Rencana route:

```php
Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('categories', CategoryController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('warehouses', WarehouseController::class);
Route::resource('products', ProductController::class);

Route::get('stock-movements', [StockMovementController::class, 'index'])
    ->name('stock-movements.index');

Route::get('stock-movements/create', [StockMovementController::class, 'create'])
    ->name('stock-movements.create');

Route::post('stock-movements', [StockMovementController::class, 'store'])
    ->name('stock-movements.store');
```

Setelah auth ditambahkan:

```php
Route::middleware('auth')->group(function () {
    // semua route inventori
});
```

Catatan:

- `StockMovement` tidak perlu full resource di awal. Untuk latihan, cukup `index`, `create`, dan `store`.
- Hindari edit/delete stock movement di MVP karena riwayat stok idealnya immutable. Kalau salah input, buat movement koreksi.

## 7. Controller

Gunakan resource controller untuk CRUD standar.

Controller yang dibuat:

- `DashboardController`
- `CategoryController`
- `SupplierController`
- `WarehouseController`
- `ProductController`
- `StockMovementController`

### 7.1 `DashboardController`

Tugas:

- Menampilkan total produk.
- Menampilkan total kategori.
- Menampilkan total supplier.
- Menampilkan total gudang.
- Menampilkan produk dengan stok rendah.
- Menampilkan pergerakan stok terbaru.

### 7.2 `CategoryController`

Tugas:

- `index`: list kategori dengan search dan pagination.
- `create`: form tambah.
- `store`: validasi dan simpan.
- `edit`: form edit.
- `update`: validasi dan update.
- `destroy`: hapus jika belum dipakai produk.

### 7.3 `SupplierController`

Mirip dengan category, tetapi field lebih banyak.

Fitur:

- Search berdasarkan nama, contact, email, phone.
- Cegah delete jika supplier masih dipakai produk, atau gunakan soft delete nanti.

### 7.4 `WarehouseController`

Fitur:

- CRUD gudang.
- Search berdasarkan nama dan kode.
- Cegah delete jika gudang masih punya stok atau stock movement.

### 7.5 `ProductController`

Tugas:

- `index`: list produk dengan filter kategori, supplier, status stok, dan search.
- `create`: form produk dengan dropdown kategori dan supplier.
- `store`: validasi dan simpan produk.
- `show`: detail produk, stok per gudang, dan riwayat movement.
- `edit`: form edit.
- `update`: validasi dan update produk.
- `destroy`: hapus jika belum punya stock movement.

Catatan:

- Saat membuat produk, jangan langsung memaksa stok awal di tabel produk. Jika ingin stok awal, buat stock movement type `in` melalui service.
- SKU harus unique.

### 7.6 `StockMovementController`

Tugas:

- `index`: list riwayat stok dengan filter produk, gudang, tipe, tanggal.
- `create`: form stok masuk/keluar/adjustment.
- `store`: validasi lalu panggil service stok.

Controller ini sebaiknya tidak berisi business logic perhitungan stok. Serahkan ke service.

## 8. Form Request Validation

Buat Form Request agar validasi tidak menumpuk di controller.

Command:

```bash
php artisan make:request StoreCategoryRequest
php artisan make:request UpdateCategoryRequest
php artisan make:request StoreSupplierRequest
php artisan make:request UpdateSupplierRequest
php artisan make:request StoreWarehouseRequest
php artisan make:request UpdateWarehouseRequest
php artisan make:request StoreProductRequest
php artisan make:request UpdateProductRequest
php artisan make:request StoreStockMovementRequest
```

Contoh aturan penting:

- Category `name`: required, max 255, unique.
- Product `sku`: required, max 100, unique.
- Product `category_id`: required, exists categories.
- Product `supplier_id`: nullable, exists suppliers.
- Stock movement `type`: required, in `in,out,adjustment`.
- Stock movement `quantity`: required, integer, min 1.
- Stock movement `movement_date`: required, date.

Untuk update, unique rule harus mengabaikan record saat ini.

## 9. Service Layer

Service layer penting untuk fitur stok karena ada aturan bisnis dan transaksi database.

Folder:

```text
app/Services/Inventory/
```

Service yang disarankan:

- `StockMovementService`
- `ProductService` jika logic produk mulai membesar

### 9.1 `StockMovementService`

Method utama:

```php
public function createMovement(array $data, ?User $user = null): StockMovement
```

Tugas method:

1. Mulai database transaction.
2. Ambil atau buat record `product_stocks` berdasarkan `product_id` dan `warehouse_id`.
3. Simpan `stock_before`.
4. Hitung `stock_after`.
5. Tolak stok keluar jika stok tidak cukup.
6. Update `product_stocks.quantity`.
7. Buat record `stock_movements`.
8. Commit transaction.
9. Return `StockMovement`.

Pseudo-flow:

```php
DB::transaction(function () use ($data, $user) {
    $stock = ProductStock::query()
        ->where('product_id', $data['product_id'])
        ->where('warehouse_id', $data['warehouse_id'])
        ->lockForUpdate()
        ->firstOrCreate([...], ['quantity' => 0]);

    $before = $stock->quantity;
    $after = match ($data['type']) {
        'in' => $before + $data['quantity'],
        'out' => $before - $data['quantity'],
        'adjustment' => $data['quantity'],
    };

    if ($after < 0) {
        throw ValidationException::withMessages([
            'quantity' => 'Stok tidak mencukupi.',
        ]);
    }

    $stock->update(['quantity' => $after]);

    return StockMovement::create([...]);
});
```

Catatan:

- `lockForUpdate()` membantu mencegah stok kacau saat dua transaksi berjalan bersamaan.
- Untuk `adjustment`, tentukan dari awal apakah `quantity` berarti jumlah akhir atau selisih. Rekomendasi untuk pemula: `quantity` berarti jumlah akhir agar mudah dipahami.

## 10. View dan UI Blade

Struktur view:

```text
resources/views/layouts/app.blade.php
resources/views/dashboard.blade.php
resources/views/categories/index.blade.php
resources/views/categories/create.blade.php
resources/views/categories/edit.blade.php
resources/views/suppliers/*
resources/views/warehouses/*
resources/views/products/*
resources/views/stock-movements/*
```

Komponen Blade yang berguna:

```text
resources/views/components/alert.blade.php
resources/views/components/input-error.blade.php
resources/views/components/form-field.blade.php
resources/views/components/status-badge.blade.php
```

Prinsip UI:

- Sidebar atau navbar sederhana.
- Tabel untuk list data.
- Form yang jelas dan konsisten.
- Flash message setelah create/update/delete.
- Pagination di semua list besar.
- Tombol aksi jelas: detail, edit, hapus.
- Konfirmasi sebelum delete.

## 11. Search, Filter, dan Pagination

Implementasikan bertahap setelah CRUD dasar selesai.

Category:

- Search `name`.

Supplier:

- Search `name`, `contact_name`, `email`, `phone`.

Product:

- Search `name`, `sku`.
- Filter `category_id`.
- Filter `supplier_id`.
- Filter `is_active`.
- Filter stok rendah.

StockMovement:

- Filter `product_id`.
- Filter `warehouse_id`.
- Filter `type`.
- Filter tanggal awal dan tanggal akhir.

Gunakan query string agar filter tetap ada saat pagination:

```php
->paginate(10)
->withQueryString();
```

## 12. Seeder dan Factory

Seeder berguna agar aplikasi langsung punya data latihan.

Factory yang dibuat:

- `CategoryFactory`
- `SupplierFactory`
- `WarehouseFactory`
- `ProductFactory`

Seeder awal:

- 5 kategori.
- 5 supplier.
- 2 gudang.
- 20 produk.
- Beberapa stock movement masuk.

Command:

```bash
php artisan make:factory CategoryFactory --model=Category
php artisan make:factory SupplierFactory --model=Supplier
php artisan make:factory WarehouseFactory --model=Warehouse
php artisan make:factory ProductFactory --model=Product
```

## 13. Testing

Minimal test yang sebaiknya dibuat:

- User bisa melihat list kategori.
- User bisa membuat kategori.
- User tidak bisa membuat kategori tanpa nama.
- User bisa membuat produk dengan kategori valid.
- SKU produk harus unique.
- Stok masuk menambah `product_stocks.quantity`.
- Stok keluar mengurangi `product_stocks.quantity`.
- Stok keluar gagal jika quantity melebihi stok.
- Stock movement menyimpan `stock_before` dan `stock_after`.

Command:

```bash
php artisan make:test CategoryTest
php artisan make:test ProductTest
php artisan make:test StockMovementTest
```

Jalankan test:

```bash
php artisan test
```

## 14. Auth dan Authorization

Untuk awal, aplikasi bisa dibuat tanpa auth agar fokus ke CRUD dan relasi. Setelah alur utama stabil, tambahkan auth.

Opsi:

- Laravel Breeze jika tersedia untuk Laravel versi yang dipakai.
- Auth sederhana manual jika ingin latihan lebih dalam.

Setelah auth aktif:

- Semua route inventori masuk middleware `auth`.
- `created_by` diisi dengan `auth()->id()`.
- Tambahkan role sederhana:
  - `admin`: semua akses.
  - `staff`: CRUD produk dan stok, tetapi tidak bisa hapus master data.

Authorization bisa memakai:

- Policy.
- Middleware custom role.
- Gate sederhana di `AppServiceProvider`.

## 15. Error Handling dan Validasi Bisnis

Hal yang wajib ditangani:

- Tidak boleh hapus kategori yang masih dipakai produk.
- Tidak boleh hapus supplier yang masih dipakai produk.
- Tidak boleh hapus gudang yang masih punya stok atau riwayat stok.
- Tidak boleh hapus produk yang sudah punya stock movement.
- Tidak boleh stok keluar melebihi stok tersedia.
- SKU harus unique.
- Harga tidak boleh negatif.
- Minimum stock tidak boleh negatif.

Gunakan:

- Form Request untuk validasi input.
- Service untuk validasi bisnis stok.
- Flash message untuk feedback user.

## 16. Rekomendasi Struktur Folder

Struktur kode yang disarankan:

```text
app/
  Http/
    Controllers/
      CategoryController.php
      SupplierController.php
      WarehouseController.php
      ProductController.php
      StockMovementController.php
      DashboardController.php
    Requests/
      StoreCategoryRequest.php
      UpdateCategoryRequest.php
      StoreSupplierRequest.php
      UpdateSupplierRequest.php
      StoreWarehouseRequest.php
      UpdateWarehouseRequest.php
      StoreProductRequest.php
      UpdateProductRequest.php
      StoreStockMovementRequest.php
  Models/
    Category.php
    Supplier.php
    Warehouse.php
    Product.php
    ProductStock.php
    StockMovement.php
  Services/
    Inventory/
      StockMovementService.php
```

## 17. Tahapan Implementasi

### Tahap 1: Fondasi Project

- Pastikan `.env` database sudah benar.
- Jalankan migration bawaan.
- Buat layout Blade dasar.
- Ubah route `/` menjadi dashboard sederhana.

Target selesai:

- Aplikasi bisa dibuka.
- Layout utama siap dipakai semua halaman.

### Tahap 2: Master Data Dasar

- Buat `Category` migration, model, controller, request, view.
- Buat `Supplier` migration, model, controller, request, view.
- Buat `Warehouse` migration, model, controller, request, view.

Target selesai:

- CRUD category, supplier, dan warehouse berjalan.
- Ada search dan pagination sederhana.

### Tahap 3: Produk dan Relasi

- Buat `Product` migration, model, controller, request, view.
- Tambahkan relasi ke category dan supplier.
- Buat halaman detail produk.
- Tampilkan kategori dan supplier di tabel produk.

Target selesai:

- Produk bisa dibuat dengan relasi category dan supplier.
- Product detail menampilkan informasi lengkap.

### Tahap 4: Stok

- Buat `ProductStock` migration dan model.
- Buat `StockMovement` migration, model, controller, request.
- Buat `StockMovementService`.
- Implementasikan stok masuk dan stok keluar.
- Tampilkan stok per gudang di detail produk.

Target selesai:

- Stok masuk menambah stok.
- Stok keluar mengurangi stok.
- Stok keluar tidak bisa melebihi stok tersedia.
- Riwayat stok tersimpan.

### Tahap 5: Dashboard dan Laporan Ringkas

- Dashboard total produk, kategori, supplier, gudang.
- Dashboard produk stok rendah.
- Dashboard pergerakan stok terbaru.
- Halaman stock movement dengan filter.

Target selesai:

- Aplikasi mulai terasa seperti sistem inventori nyata.

### Tahap 6: Testing dan Perapihan

- Tambahkan feature test untuk CRUD penting.
- Tambahkan test untuk service stok.
- Rapikan validasi.
- Rapikan flash message.
- Jalankan Pint dan test.

Target selesai:

- Fitur inti punya perlindungan test.
- Struktur kode lebih siap dikembangkan.

### Tahap 7: Auth dan Role

- Tambahkan auth.
- Protect semua route inventori.
- Isi `created_by`.
- Tambahkan role admin/staff bila perlu.

Target selesai:

- Aplikasi punya login.
- Aktivitas stok bisa dilacak ke user.

## 18. Command Checklist

Command awal yang kemungkinan akan sering dipakai:

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan make:model Category -mcr
php artisan make:request StoreCategoryRequest
php artisan make:request UpdateCategoryRequest
php artisan make:test CategoryTest
php artisan test
./vendor/bin/pint
```

Jika memakai script Composer:

```bash
composer test
composer run dev
```

## 19. Definisi Selesai untuk MVP

MVP dianggap selesai jika:

- CRUD category berjalan.
- CRUD supplier berjalan.
- CRUD warehouse berjalan.
- CRUD product berjalan.
- Produk terhubung ke category dan supplier.
- Stok masuk berjalan.
- Stok keluar berjalan.
- Stok keluar gagal jika stok kurang.
- Detail produk menampilkan stok per gudang.
- Riwayat stok bisa dilihat dan difilter.
- Dashboard menampilkan ringkasan.
- Minimal test untuk stok masuk/keluar lulus.

## 20. Prioritas Belajar

Urutan belajar yang paling bernilai:

1. Migration dan foreign key.
2. Eloquent relationship.
3. Resource controller dan route model binding.
4. Form request validation.
5. Blade form dan table.
6. Service class dan database transaction.
7. Testing.
8. Auth dan authorization.

Jika bingung saat mulai, jangan mulai dari dashboard. Mulai dari `Category`, lalu `Supplier`, lalu `Warehouse`, lalu `Product`. Setelah itu baru masuk ke stok, karena fitur stok membutuhkan relasi yang sudah siap.
