<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cashier\CashierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Station\BaristaController;
use App\Http\Controllers\Station\KitchenController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Waiter\WaiterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Pitou Cafe POS (Setup Phase)
|--------------------------------------------------------------------------
| Referensi: SRS §7 (Route Mapping) & §2.1 (Matriks Akses Role).
| Fase ini HANYA landing/dashboard placeholder per role. Belum ada CRUD.
*/

// Root: arahkan sesuai status login & role.
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route(auth()->user()->dashboardRoute());
})->name('home');

/*
| Auth (FR-001) — hanya untuk tamu
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
| Area terproteksi (harus login)
*/
Route::middleware('auth')->group(function () {

    // Admin (FR-010 dashboard, FR-002 user management)
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // User Management (FR-002)
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('admin.users.toggle-status');

        Route::resource('users', UserController::class)
            ->except(['show'])
            ->names('admin.users');

        // Manajemen Menu & Kategori (FR-003)
        Route::patch('menus/{menu}/toggle-availability', [MenuController::class, 'toggleAvailability'])
            ->name('admin.menus.toggle-availability');

        Route::resource('menus', MenuController::class)
            ->except(['show'])
            ->names('admin.menus');

        Route::resource('categories', CategoryController::class)
            ->except(['show'])
            ->names('admin.categories');

        // Manajemen Meja (FR-004) — status meja dikelola sistem, tanpa route toggle.
        Route::resource('tables', TableController::class)
            ->except(['show'])
            ->names('admin.tables');

        // Pengaturan sistem (BR-016 pajak dinamis — hanya Admin)
        Route::get('/settings', [SettingController::class, 'edit'])->name('admin.settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

        // Riwayat Transaksi + Invoice PDF (FR-009) — read-only
        Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
        Route::get('/transactions/{payment}', [TransactionController::class, 'show'])->name('admin.transactions.show');
        Route::get('/transactions/{payment}/invoice', [TransactionController::class, 'invoice'])->name('admin.transactions.invoice');

        // Laporan Penjualan (FR-011) — read-only, sesuai SRS §7
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
    });

    // Waiters (FR-005, FR-012, FR-013)
    Route::middleware('role:waiters')->group(function () {
        Route::get('/waiter', [WaiterController::class, 'index'])->name('waiter.dashboard');

        // Buat order (FR-005)
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

        // Menu (Stok) — FR-012: toggle tersedia/habis oleh Waiters
        Route::get('/waiter/menus', [WaiterController::class, 'menus'])->name('waiter.menus');
        Route::patch('/menus/{menu}/availability', [WaiterController::class, 'toggleAvailability'])
            ->name('waiter.menus.availability');

        // Pesanan aktif — FR-013 (read-only)
        Route::get('/waiter/orders', [WaiterController::class, 'orders'])->name('waiter.orders');
    });

    // Station Kitchen (FR-006)
    Route::middleware('role:kitchen')->group(function () {
        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
        Route::get('/kitchen/queue-status', [KitchenController::class, 'queueStatus'])->name('kitchen.queue-status');
        Route::get('/orders/{order}/checker/kitchen', [KitchenController::class, 'checker'])->name('kitchen.checker');
    });

    // Station Barista (FR-007)
    Route::middleware('role:barista')->group(function () {
        Route::get('/barista', [BaristaController::class, 'index'])->name('barista.index');
        Route::get('/barista/queue-status', [BaristaController::class, 'queueStatus'])->name('barista.queue-status');
        Route::get('/orders/{order}/checker/barista', [BaristaController::class, 'checker'])->name('barista.checker');
    });

    // Kasir (FR-008)
    Route::middleware('role:kasir')->group(function () {
        Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/orders/{order}/receipt', [PaymentController::class, 'receipt'])->name('cashier.receipt');
    });
});
