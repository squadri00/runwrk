<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ops\Audit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return Auth::guard('superadmin')->check() ? redirect()->route('admin.home') : view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (! Auth::guard('superadmin')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Those details do not match.']);
        }

        $request->session()->regenerate();
        Audit::log('superadmin.login');

        return redirect()->intended(route('admin.home'));
    }

    public function logout(Request $request)
    {
        Audit::log('superadmin.logout');
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
