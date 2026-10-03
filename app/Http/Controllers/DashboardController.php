<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Category;
use App\Models\Person;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Visitor;
use App\Models\Warehouse;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $lowStockProducts = Product::query()->with(['category', 'stocks'])->withSum('stocks as total_stock', 'quantity')->where('is_active', true)->get()->filter(fn (Product $p) => (int) ($p->total_stock ?? 0) < $p->minimum_stock)->sortBy('total_stock')->take(8);
        $recentMovements = StockMovement::query()->with(['product:id,name,sku', 'warehouse:id,name', 'creator:id,name'])->latest()->limit(8)->get();
        $facility = auth()->user()->facility;
        $today = now()->toDateString();
        $attendanceQuery = Attendance::query()->whereDate('attendance_date', $today);
        $inventoryCost = Product::query()->where('is_active', true)->withSum('stocks as total_stock', 'quantity')->get()->sum(fn ($p) => (float) $p->purchase_price * (int) ($p->total_stock ?? 0));
        $inventoryRetail = Product::query()->where('is_active', true)->withSum('stocks as total_stock', 'quantity')->get()->sum(fn ($p) => (float) $p->selling_price * (int) ($p->total_stock ?? 0));
        $stats = [
            'products' => Product::count(), 'categories' => Category::count(), 'suppliers' => Supplier::count(), 'warehouses' => Warehouse::count(),
            'people' => Person::when($facility, fn ($q) => $q->where('facility_id', $facility->id))->where('status', 'active')->count(),
            'guards' => Person::when($facility, fn ($q) => $q->where('facility_id', $facility->id))->where('person_type', 'security_guard')->where('status', 'active')->count(),
            'present' => (clone $attendanceQuery)->whereIn('status', ['present', 'late'])->count(),
            'absent' => (clone $attendanceQuery)->where('status', 'absent')->count(),
            'late' => (clone $attendanceQuery)->where('status', 'late')->count(),
            'overtime' => (clone $attendanceQuery)->sum('overtime_minutes'),
            'visitors' => Visitor::when($facility, fn ($q) => $q->where('facility_id', $facility->id))->count(),
            'inventory_cost' => $inventoryCost, 'inventory_retail' => $inventoryRetail, 'expected_profit' => $inventoryRetail - $inventoryCost,
            'out_of_stock' => Product::where('is_active', true)->withSum('stocks as total_stock', 'quantity')->get()->filter(fn ($p) => (int) ($p->total_stock ?? 0) <= 0)->count(),
        ];
        $chart = collect(range(6, 0))->map(function ($days) use ($facility) {
            $date = now()->subDays($days)->toDateString();
            $attendance = Attendance::whereDate('attendance_date', $date)
                ->when($facility, fn ($q) => $q->whereHas('person', fn ($p) => $p->where('facility_id', $facility->id)))
                ->whereIn('status', ['present', 'late'])
                ->count();

            return [
                'label' => now()->subDays($days)->format('m/d'),
                'in' => StockMovement::whereDate('movement_date', $date)->where('type', 'in')->sum('quantity'),
                'out' => StockMovement::whereDate('movement_date', $date)->where('type', 'out')->sum('quantity'),
                'attendance' => $attendance,
            ];
        });

        return view('dashboard', compact('lowStockProducts','recentMovements','facility','stats','chart'));
    }
}
