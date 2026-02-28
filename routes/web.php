<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:view-dashboard'])
    ->name('dashboard');

Route::middleware(['auth', 'activity.log'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('permission:process-sales')->group(function () {
        Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
        Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');
        Route::get('/cashier/receipt/{transaction}', [CashierController::class, 'receipt'])->name('cashier.receipt');
        Route::get('/cashier/receipt/{transaction}/export-pdf', [CashierController::class, 'exportPdf'])->name('cashier.receipt.export-pdf');
        Route::post('/cashier/receipt/{transaction}/generate-pdf', [CashierController::class, 'generateReceiptPdf'])->name('cashier.receipt.generate-pdf');
    });

    // Categories - Granular Permissions
    Route::get('/categories', [CategoryController::class, 'index'])->middleware('permission:view-categories')->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->middleware('permission:create-categories')->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('permission:create-categories')->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->middleware('permission:edit-categories')->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('permission:edit-categories')->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete-categories')->name('categories.destroy');

    // Products - Granular Permissions
    Route::get('/products', [ProductController::class, 'index'])->middleware('permission:view-products')->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->middleware('permission:create-products')->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->middleware('permission:create-products')->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:edit-products')->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:edit-products')->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:delete-products')->name('products.destroy');

    // Stock Management
    Route::get('/stock', [StockController::class, 'index'])->middleware('permission:view-stock')->name('stock.index');
    Route::get('/stock/{product}/adjust', [StockController::class, 'adjust'])->middleware('permission:adjust-stock')->name('stock.adjust');
    Route::post('/stock/{product}/adjust', [StockController::class, 'storeAdjustment'])->middleware('permission:adjust-stock')->name('stock.storeAdjustment');
    Route::get('/stock/history', [StockController::class, 'history'])->middleware('permission:view-stock')->name('stock.history');

    Route::middleware('permission:view-reports')->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/sales/excel', [ReportController::class, 'exportSalesExcel'])->name('reports.sales.excel');
        Route::get('/reports/sales/pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.sales.pdf');
        Route::get('/reports/sales/{transaction}', [ReportController::class, 'show'])->name('reports.show');
    });

    Route::middleware('permission:export-reports')->group(function () {
        // Backward compatibility routes (optional)
    });

    Route::middleware('permission:view-activity-logs')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // Users - Granular Permissions
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:view-users')->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:create-users')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:create-users')->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:edit-users')->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:edit-users')->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:delete-users')->name('users.destroy');

    // Roles - Granular Permissions
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:view-roles')->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->middleware('permission:create-roles')->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:create-roles')->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:edit-roles')->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:edit-roles')->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-roles')->name('roles.destroy');

    // Permissions - Granular Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:view-permissions')->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->middleware('permission:create-permissions')->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:create-permissions')->name('permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->middleware('permission:edit-permissions')->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:edit-permissions')->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:delete-permissions')->name('permissions.destroy');

    // Settings - Granular Permissions
    Route::get('/settings', [SettingsController::class, 'index'])->middleware('permission:view-settings')->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->middleware('permission:edit-settings')->name('settings.update');
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->middleware('permission:edit-settings')->name('settings.reset');
});

require __DIR__.'/auth.php';
