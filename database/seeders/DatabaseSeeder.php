<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Category;
use App\Models\Department;
use App\Models\Facility;
use App\Models\PaymentMethod;
use App\Models\Person;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Shift;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic inventory data.
     */
    public function run(): void
    {
        // ── Admin user ────────────────────────────────────────────────────────
        $admin = User::factory()->create([
            'name' => 'Admin Inventori',
            'email' => 'admin@inventori.test',
            'password' => Hash::make('password'),
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        // ── Facility / people / attendance demo data
        $facility = Facility::create([
            'name' => 'شركة Laraventry للتجارة',
            'code' => 'FAC-001',
            'type' => 'company',
            'phone' => '+970 59 000 0000',
            'email' => 'info@laraventry.test',
            'address' => 'Gaza',
            'description' => 'منشأة تجريبية لإدارة الموظفين والمخزون والأمن.',
        ]);
        $admin->forceFill(['facility_id' => $facility->id])->save();
        $departments = collect(['الإدارة', 'الموارد البشرية', 'المبيعات', 'المستودعات', 'الأمن', 'الصيانة'])->map(fn ($name) => Department::create(['facility_id' => $facility->id, 'name' => $name, 'code' => strtoupper(substr(md5($name), 0, 5))]));
        $dayShift = Shift::create(['facility_id' => $facility->id, 'name' => 'الدوام الصباحي', 'start_time' => '08:00', 'end_time' => '16:00', 'grace_minutes' => 10, 'break_minutes' => 60]);
        $nightShift = Shift::create(['facility_id' => $facility->id, 'name' => 'الوردية الليلية', 'start_time' => '20:00', 'end_time' => '08:00', 'grace_minutes' => 10, 'break_minutes' => 30, 'is_overnight' => true]);
        $manager = Person::create(['facility_id' => $facility->id, 'department_id' => $departments->first()->id, 'shift_id' => $dayShift->id, 'user_id' => $admin->id, 'employee_number' => 'EMP-0001', 'full_name' => $admin->name, 'person_type' => 'manager', 'job_title' => 'مدير النظام', 'email' => $admin->email, 'join_date' => now()->subYear()->toDateString()]);
        $security = Person::create(['facility_id' => $facility->id, 'department_id' => $departments->where('name', 'الأمن')->first()->id, 'shift_id' => $nightShift->id, 'employee_number' => 'SEC-0001', 'full_name' => 'أحمد حارس الأمن', 'person_type' => 'security_guard', 'job_title' => 'حارس أمن', 'phone' => '0590000001', 'join_date' => now()->subMonths(8)->toDateString(), 'security_company' => 'شركة الأمان', 'security_permit_number' => 'SEC-2026-001', 'security_permit_expires_at' => now()->addMonths(8)->toDateString(), 'guard_post' => 'البوابة الرئيسية']);
        $employee = Person::create(['facility_id' => $facility->id, 'department_id' => $departments->where('name', 'المبيعات')->first()->id, 'shift_id' => $dayShift->id, 'employee_number' => 'EMP-0002', 'full_name' => 'محمد أحمد', 'person_type' => 'employee', 'job_title' => 'موظف مبيعات', 'phone' => '0590000002', 'join_date' => now()->subMonths(5)->toDateString()]);
        foreach ([$manager, $security, $employee] as $person) {
            Attendance::create(['person_id' => $person->id, 'attendance_date' => now()->toDateString(), 'scheduled_start' => $person->shift?->start_time, 'scheduled_end' => $person->shift?->end_time, 'check_in' => $person === $security ? '20:02' : '08:05', 'check_out' => $person === $security ? '08:20' : '17:30', 'worked_minutes' => $person === $security ? 690 : 505, 'late_minutes' => $person === $security ? 0 : 0, 'overtime_minutes' => $person === $security ? 20 : 90, 'status' => 'present']);
        }
        Visitor::create(['facility_id' => $facility->id, 'name' => 'زائر تجريبي', 'phone' => '0590000010', 'company' => 'شركة خارجية', 'host_name' => 'محمد أحمد', 'purpose' => 'اجتماع', 'visitor_number' => 'VIS-DEMO-001']);

        // ── Payment methods ─────────────────────────────────────────────────
        PaymentMethod::create([
            'name' => 'Bank Transfer', 'provider' => 'International Bank', 'scope' => 'global',
            'account_name' => 'Laraventry Pro', 'account_number' => 'IBAN / SWIFT - DEMO', 'currency' => 'USD',
            'instructions' => 'Use your transaction reference and upload the official receipt.', 'is_active' => true, 'sort_order' => 1,
        ]);
        PaymentMethod::create([
            'name' => 'Local Bank Transfer', 'provider' => 'Local Bank', 'scope' => 'local',
            'account_name' => 'Laraventry Pro', 'account_number' => 'LOCAL-ACCOUNT-DEMO', 'currency' => 'ILS',
            'instructions' => 'Transfer the amount then upload the receipt for manual verification.', 'is_active' => true, 'sort_order' => 2,
        ]);
        PaymentMethod::create([
            'name' => 'PayPal / Online Wallet', 'provider' => 'PayPal', 'scope' => 'global',
            'account_name' => 'Laraventry Pro', 'account_number' => 'paypal@example.test', 'currency' => 'USD',
            'instructions' => 'Complete the payment on the configured account and upload the receipt.', 'is_active' => true, 'sort_order' => 3,
        ]);

        // ── Master data ───────────────────────────────────────────────────────
        $categories = $this->seedCategories();
        $suppliers = $this->seedSuppliers();
        $warehouses = $this->seedWarehouses();

        // ── Products ──────────────────────────────────────────────────────────
        $products = $this->seedProducts($categories, $suppliers, $admin);

        // ── Stock movements ───────────────────────────────────────────────────
        $this->seedStockMovements($products, $warehouses, $admin);
    }

    private function seedCategories(): Collection
    {
        $data = [
            ['name' => 'Elektronik',          'description' => 'Peralatan dan komponen elektronik'],
            ['name' => 'Alat Tulis Kantor',   'description' => 'Perlengkapan kantor dan tulis-menulis'],
            ['name' => 'Bahan Bangunan',       'description' => 'Material dan perlengkapan konstruksi'],
            ['name' => 'Peralatan Rumah Tangga', 'description' => 'Barang kebutuhan rumah tangga'],
            ['name' => 'Makanan & Minuman',    'description' => 'Produk makanan dan minuman'],
        ];

        return collect($data)->map(fn ($item) => Category::create([
            'name' => $item['name'],
            'slug' => Str::slug($item['name']),
            'description' => $item['description'],
            'is_active' => true,
        ]));
    }

    private function seedSuppliers(): Collection
    {
        $data = [
            ['name' => 'PT Maju Bersama',   'contact_name' => 'Budi Santoso',   'phone' => '021-5551234', 'email' => 'budi@majubersama.co.id'],
            ['name' => 'CV Sumber Jaya',    'contact_name' => 'Siti Rahayu',    'phone' => '022-5559876', 'email' => 'siti@sumberjaya.com'],
            ['name' => 'UD Karya Mandiri',  'contact_name' => 'Ahmad Fauzi',    'phone' => '031-5554321', 'email' => 'ahmad@karyamandiri.id'],
            ['name' => 'PT Global Niaga',   'contact_name' => 'Dewi Lestari',   'phone' => '021-5557890', 'email' => 'dewi@globalniaga.co.id'],
            ['name' => 'CV Berkah Abadi',   'contact_name' => 'Rizki Pratama',  'phone' => '024-5552468', 'email' => 'rizki@berkababadi.com'],
        ];

        return collect($data)->map(fn ($item) => Supplier::create([
            ...$item,
            'address' => fake()->address(),
            'is_active' => true,
        ]));
    }

    private function seedWarehouses(): Collection
    {
        $data = [
            ['name' => 'Gudang Jakarta Pusat', 'code' => 'WH-JKT-01', 'address' => 'Jl. Sudirman No. 1, Jakarta Pusat'],
            ['name' => 'Gudang Bekasi',        'code' => 'WH-BKS-01', 'address' => 'Kawasan Industri MM2100, Bekasi'],
        ];

        return collect($data)->map(fn ($item) => Warehouse::create([
            ...$item,
            'is_active' => true,
        ]));
    }

    private function seedProducts(
        Collection $categories,
        Collection $suppliers,
        User $admin
    ): Collection {
        $productData = [
            ['name' => 'Laptop ASUS VivoBook 15',   'sku' => 'ELEC-001', 'category' => 'Elektronik',          'unit' => 'pcs',  'purchase_price' => 7500000, 'selling_price' => 9000000, 'min_stock' => 5],
            ['name' => 'Mouse Wireless Logitech',    'sku' => 'ELEC-002', 'category' => 'Elektronik',          'unit' => 'pcs',  'purchase_price' => 150000,  'selling_price' => 250000,  'min_stock' => 10],
            ['name' => 'Keyboard Mechanical',        'sku' => 'ELEC-003', 'category' => 'Elektronik',          'unit' => 'pcs',  'purchase_price' => 350000,  'selling_price' => 500000,  'min_stock' => 5],
            ['name' => 'Monitor LG 24 inch',         'sku' => 'ELEC-004', 'category' => 'Elektronik',          'unit' => 'pcs',  'purchase_price' => 2000000, 'selling_price' => 2800000, 'min_stock' => 3],
            ['name' => 'Pulpen Pilot G2',            'sku' => 'ATK-001',  'category' => 'Alat Tulis Kantor',   'unit' => 'pcs',  'purchase_price' => 8000,    'selling_price' => 15000,   'min_stock' => 50],
            ['name' => 'Kertas HVS A4 Sinar Dunia',  'sku' => 'ATK-002',  'category' => 'Alat Tulis Kantor',   'unit' => 'rim',  'purchase_price' => 40000,   'selling_price' => 55000,   'min_stock' => 30],
            ['name' => 'Stapler Joyko No. 10',       'sku' => 'ATK-003',  'category' => 'Alat Tulis Kantor',   'unit' => 'pcs',  'purchase_price' => 15000,   'selling_price' => 25000,   'min_stock' => 20],
            ['name' => 'Binder A4 Ring 2',           'sku' => 'ATK-004',  'category' => 'Alat Tulis Kantor',   'unit' => 'pcs',  'purchase_price' => 20000,   'selling_price' => 35000,   'min_stock' => 15],
            ['name' => 'Semen Tiga Roda 50kg',       'sku' => 'BNG-001',  'category' => 'Bahan Bangunan',      'unit' => 'sak',  'purchase_price' => 65000,   'selling_price' => 80000,   'min_stock' => 20],
            ['name' => 'Cat Tembok Dulux 5L',        'sku' => 'BNG-002',  'category' => 'Bahan Bangunan',      'unit' => 'kaleng', 'purchase_price' => 120000, 'selling_price' => 165000,  'min_stock' => 10],
            ['name' => 'Paku 5cm (1kg)',             'sku' => 'BNG-003',  'category' => 'Bahan Bangunan',      'unit' => 'kg',   'purchase_price' => 15000,   'selling_price' => 25000,   'min_stock' => 30],
            ['name' => 'Ember 20L Maspion',          'sku' => 'PRT-001',  'category' => 'Peralatan Rumah Tangga', 'unit' => 'pcs', 'purchase_price' => 25000,  'selling_price' => 45000,   'min_stock' => 15],
            ['name' => 'Sapu Lidi',                  'sku' => 'PRT-002',  'category' => 'Peralatan Rumah Tangga', 'unit' => 'pcs', 'purchase_price' => 8000,   'selling_price' => 15000,   'min_stock' => 20],
            ['name' => 'Minyak Goreng Bimoli 2L',    'sku' => 'FNB-001',  'category' => 'Makanan & Minuman',   'unit' => 'botol', 'purchase_price' => 28000,  'selling_price' => 38000,   'min_stock' => 24],
            ['name' => 'Gula Pasir 1kg',             'sku' => 'FNB-002',  'category' => 'Makanan & Minuman',   'unit' => 'kg',   'purchase_price' => 13000,   'selling_price' => 18000,   'min_stock' => 50],
            ['name' => 'Beras Premium 5kg',          'sku' => 'FNB-003',  'category' => 'Makanan & Minuman',   'unit' => 'karung', 'purchase_price' => 62000, 'selling_price' => 78000,   'min_stock' => 30],
            ['name' => 'Teh Celup Sosro 25s',        'sku' => 'FNB-004',  'category' => 'Makanan & Minuman',   'unit' => 'box',  'purchase_price' => 8000,    'selling_price' => 13000,   'min_stock' => 40],
            ['name' => 'Flashdisk SanDisk 64GB',     'sku' => 'ELEC-005', 'category' => 'Elektronik',          'unit' => 'pcs',  'purchase_price' => 100000,  'selling_price' => 160000,  'min_stock' => 15],
            ['name' => 'Lampu LED Philips 10W',      'sku' => 'ELEC-006', 'category' => 'Elektronik',          'unit' => 'pcs',  'purchase_price' => 25000,   'selling_price' => 45000,   'min_stock' => 20],
            ['name' => 'Kabel USB Type-C 1m',        'sku' => 'ELEC-007', 'category' => 'Elektronik',          'unit' => 'pcs',  'purchase_price' => 15000,   'selling_price' => 30000,   'min_stock' => 25],
        ];

        $categoryMap = $categories->keyBy('name');
        $supplierList = $suppliers->pluck('id')->all();

        return collect($productData)->map(fn ($data) => Product::create([
            'category_id' => $categoryMap[$data['category']]->id,
            'supplier_id' => fake()->boolean(70) ? fake()->randomElement($supplierList) : null,
            'created_by' => $admin->id,
            'name' => $data['name'],
            'sku' => $data['sku'],
            'description' => null,
            'unit' => $data['unit'],
            'purchase_price' => $data['purchase_price'],
            'selling_price' => $data['selling_price'],
            'minimum_stock' => $data['min_stock'],
            'is_active' => true,
        ]));
    }

    private function seedStockMovements(
        Collection $products,
        Collection $warehouses,
        User $admin
    ): void {
        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                // Skip some combinations to create varied data
                if (fake()->boolean(20)) {
                    continue;
                }

                $initialQty = fake()->numberBetween(10, 100);

                // Create initial stock via 'in' movement
                $stock = ProductStock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity' => $initialQty,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'created_by' => $admin->id,
                    'type' => 'in',
                    'quantity' => $initialQty,
                    'stock_before' => 0,
                    'stock_after' => $initialQty,
                    'reference_number' => 'PO-SEED-'.strtoupper(Str::random(6)),
                    'movement_date' => now()->subDays(fake()->numberBetween(30, 90)),
                    'notes' => 'Stok awal seeding',
                ]);

                // Add some random additional movements per product-warehouse pair
                $extraMovements = fake()->numberBetween(0, 3);
                $currentQty = $initialQty;

                for ($i = 0; $i < $extraMovements; $i++) {
                    $type = fake()->randomElement(['in', 'out']);
                    $qty = fake()->numberBetween(1, min(20, $currentQty ?: 20));

                    if ($type === 'out' && $currentQty < $qty) {
                        continue; // skip if not enough stock
                    }

                    $before = $currentQty;
                    $currentQty = $type === 'in' ? $currentQty + $qty : $currentQty - $qty;

                    StockMovement::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'created_by' => $admin->id,
                        'type' => $type,
                        'quantity' => $qty,
                        'stock_before' => $before,
                        'stock_after' => $currentQty,
                        'reference_number' => null,
                        'movement_date' => now()->subDays(fake()->numberBetween(1, 29)),
                        'notes' => null,
                    ]);
                }

                // Update the actual current stock
                $stock->update(['quantity' => $currentQty]);
            }
        }
    }
}
