<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Management\AccessLogController;
use App\Http\Controllers\Management\AttendanceController;
use App\Http\Controllers\Management\DepartmentController;
use App\Http\Controllers\Management\FacilityController;
use App\Http\Controllers\Management\PaymentController;
use App\Http\Controllers\Management\PaymentMethodController;
use App\Http\Controllers\Management\PersonController;
use App\Http\Controllers\Management\ShiftController;
use App\Http\Controllers\Management\UserManagementController;
use App\Http\Controllers\Management\VisitorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login')->name('login.attempt');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
});

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(array_key_exists($locale, config('laraventry.locales')), 404);
    session(['locale' => $locale]);

    return back();
})->name('language.switch');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('facilities', FacilityController::class)->except(['destroy'])->middleware('role:admin,hr');
    Route::resource('people', PersonController::class)->except(['destroy'])->middleware('role:admin,hr');
    Route::resource('departments', DepartmentController::class)->except(['show', 'destroy'])->middleware('role:admin,hr');
    Route::resource('shifts', ShiftController::class)->except(['show', 'destroy'])->middleware('role:admin,hr');
    Route::resource('attendance', AttendanceController::class)->except(['show', 'destroy'])->middleware('role:admin,hr');
    Route::resource('visitors', VisitorController::class)->only(['index', 'create', 'store'])->middleware('role:admin,hr,security');
    Route::resource('access-logs', AccessLogController::class)->only(['index', 'create', 'store'])->middleware('role:admin,security');
    Route::resource('users', UserManagementController::class)->only(['index', 'edit', 'update'])->middleware('role:admin');
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show'])->middleware('role:admin,hr,warehouse_manager');
    Route::post('payments/{payment}/review', [PaymentController::class, 'review'])->name('payments.review')->middleware('role:admin');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt')->middleware('role:admin,hr,warehouse_manager');
    Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index')->middleware('role:admin');
    Route::post('payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store')->middleware('role:admin');
    Route::put('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update')->middleware('role:admin');
    Route::resource('categories', CategoryController::class)->except(['show'])->middleware('role:warehouse_manager');
    Route::resource('suppliers', SupplierController::class)->except(['show'])->middleware('role:warehouse_manager');
    Route::resource('warehouses', WarehouseController::class)->except(['show'])->middleware('role:warehouse_manager');
    Route::resource('products', ProductController::class)->middleware('role:warehouse_manager');
    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index')->middleware('role:warehouse_manager');
    Route::get('stock-movements/create', [StockMovementController::class, 'create'])->name('stock-movements.create')->middleware('role:warehouse_manager');
    Route::post('stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store')->middleware('role:warehouse_manager');
});
