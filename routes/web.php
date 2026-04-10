<?php

use App\Http\Controllers\Reports\AgedCustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Forms\SalesFormController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\CompanySettingsController;

/*
|--------------------------------------------------------------------------
| Auth Routes (guests only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    Route::prefix('reports')->name('reports.')->group(function () {

        Route::prefix('reports')->name('aged-customer-analysis.')->group(function () {
            Route::get('/aged-customer-analysis', [AgedCustomerController::class, 'index'])->name('index');
            Route::get('/aged-customer-analysis/customers', [AgedCustomerController::class, 'customersBySalesman'])->name('customers');
            Route::get('/aged-customer-analysis/generate', [AgedCustomerController::class, 'generate'])->name('generate');
        });

    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Company settings
        Route::get('/company-settings', [CompanySettingsController::class, 'index'])->name('company-settings.index');
        Route::put('/company-settings', [CompanySettingsController::class, 'update'])->name('company-settings.update');

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Roles
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::patch('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions');
        });

        // Modules
        Route::prefix('modules')->name('modules.')->group(function () {
            Route::get('/', [ModuleController::class, 'index'])->name('index');
            Route::post('/', [ModuleController::class, 'store'])->name('store');
            Route::put('/{module}', [ModuleController::class, 'update'])->name('update');
            Route::delete('/{module}', [ModuleController::class, 'destroy'])->name('destroy');
        });

        // Menu Items
        Route::prefix('menu-items')->name('menu-items.')->group(function () {
            Route::get('/', [MenuItemController::class, 'index'])->name('index');
            Route::get('/create', [MenuItemController::class, 'create'])->name('create');
            Route::post('/', [MenuItemController::class, 'store'])->name('store');
            Route::get('/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('edit');
            Route::put('/{menuItem}', [MenuItemController::class, 'update'])->name('update');
            Route::delete('/{menuItem}', [MenuItemController::class, 'destroy'])->name('destroy');
            Route::post('/validate-route', [MenuItemController::class, 'validateRoute'])->name('validate-route');
        });

    });

});
