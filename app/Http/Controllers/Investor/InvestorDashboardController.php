<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestorDashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.investor-portal');
    }
}