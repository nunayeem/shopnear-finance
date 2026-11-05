
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DashboardRedirectController;

// Redirect homepage
Route::get('/', function () {
    return redirect('/dashboard');
});

// Role-based redirect after login
Route::middleware(['auth'])
    ->get('/post-login', DashboardRedirectController::class)
    ->name('post-login');

// Admin-only Finance Overview
Route::view(
    '/dashboard/finance-overview',
    'dashboard.finance-overview'
)
    ->middleware(['auth', 'tyro-dashboard.admin'])
    ->name('dashboard.finance-overview');

// Investor-only Portal
Route::view(
    '/dashboard/investor-portal',
    'dashboard.investor-portal'
)
    ->middleware(['auth', 'role:investor'])
    ->name('dashboard.investor-portal');
