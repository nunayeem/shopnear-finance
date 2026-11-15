<?php

use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $dbConnected = false;
    $dbName = null;
    $dbDriver = config('database.default', 'mysql');

    try {
        DB::connection()->getPdo();
        $dbConnected = true;
        $dbName = DB::connection()->getDatabaseName();
    } catch (Throwable $e) {
        $dbConnected = false;
    }

    return view('welcome', [
        'dbConnected' => $dbConnected,
        'dbName' => $dbName,
        'dbDriver' => $dbDriver,
    ]);
});

// Protected Web Maintenance & Deployment Routes
Route::prefix('')->controller(MaintenanceController::class)->group(function () {
    Route::match(['GET', 'POST'], '/maintenance', 'index')->name('maintenance.index');
    Route::match(['GET', 'POST'], '/maintenance/lock', 'lock')->name('maintenance.lock');
    Route::match(['GET', 'POST'], '/migrate', 'migrate')->name('maintenance.migrate');
    Route::match(['GET', 'POST'], '/migrate-status', 'migrateStatus')->name('maintenance.migrate-status');
    Route::match(['GET', 'POST'], '/optimize-clear', 'optimizeClear')->name('maintenance.optimize-clear');
    Route::match(['GET', 'POST'], '/clear-cache', 'clearCache')->name('maintenance.clear-cache');
    Route::match(['GET', 'POST'], '/clear-optimize-migrate', 'clearOptimizeMigrate')->name('maintenance.clear-optimize-migrate');
});
