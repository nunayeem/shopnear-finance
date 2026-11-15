<?php

use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Web Maintenance & Deployment Routes
Route::prefix('')->controller(MaintenanceController::class)->group(function () {
    Route::match(['GET', 'POST'], '/maintenance', 'index')->name('maintenance.index');
    Route::match(['GET', 'POST'], '/migrate', 'migrate')->name('maintenance.migrate');
    Route::match(['GET', 'POST'], '/migrate-status', 'migrateStatus')->name('maintenance.migrate-status');
    Route::match(['GET', 'POST'], '/optimize-clear', 'optimizeClear')->name('maintenance.optimize-clear');
    Route::match(['GET', 'POST'], '/clear-cache', 'clearCache')->name('maintenance.clear-cache');
    Route::match(['GET', 'POST'], '/clear-optimize-migrate', 'clearOptimizeMigrate')->name('maintenance.clear-optimize-migrate');
});
