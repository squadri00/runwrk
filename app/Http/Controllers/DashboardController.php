<?php

namespace App\Http\Controllers;

use App\Domain\Tenancy\CurrentBusiness;

class DashboardController extends Controller
{
    public function index(CurrentBusiness $current)
    {
        return view('dashboard', ['business' => $current->getOrFail()]);
    }
}
