<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ops\Audit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function show()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required']);

        $key = 'admin-login:'.strtolower($data['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Too many attempts. Try again in '.RateLimiter::availableIn($key).' seconds.']);
        }

        $provider = Auth::guard('superadmin')->getProvider();
        $admin = $provider->retrieveByCredentials(['email' => $data['email']]);

        if (! $admin || ! $provider->validateCredentials($admin, ['password' => $data['password']])) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages(['email' => 'Those details do not match.']);
        }

        RateLimiter::clear($key);

        if ($admin->hasTwoFactor()) {
            $request->session()->put('admin_2fa', ['id' => $admin->id, 'remember' => $request->boolean('remember'), 'at' => now()->timestamp]);

            return redirect()->route('admin.2fa');
        }

        return $this->complete($request, $admin, $request->boolean('remember'));
    }

    public function complete(Request $request, $admin, bool $remember)
    {
        Auth::guard('superadmin')->login($admin, $remember);
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
