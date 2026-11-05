<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('super-admin') ||
            $user->hasRole('admin')) {
            return redirect()->route('dashboard.finance-overview');
        }

        if ($user->hasRole('investor')) {
            return redirect()->route('dashboard.investor-portal');
        }

        return redirect('/dashboard');
    }
}