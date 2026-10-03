<?php

namespace App\Http\Controllers;

use App\Domain\Ops\PlatformSettings;
use App\Models\Plan;

class SiteController extends Controller
{
    public function home()
    {
        return view('site.home');
    }

    public function pricing()
    {
        return view('site.pricing', [
            'plans' => Plan::public()->get(),
            'signups' => PlatformSettings::signupsEnabled(),
        ]);
    }
}
