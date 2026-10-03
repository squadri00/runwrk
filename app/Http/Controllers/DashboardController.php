<?php

namespace App\Http\Controllers;

use App\Domain\Tenancy\CurrentBusiness;

class DashboardController extends Controller
{
    public function index(CurrentBusiness $current)
    {
        $business = $current->getOrFail()->loadCount('domains', 'users');

        return view('app.dashboard', compact('business'));
    }
}
