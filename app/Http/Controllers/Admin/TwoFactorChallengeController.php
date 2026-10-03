<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ops\Audit;
use App\Http\Controllers\Controller;
use App\Models\Superadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    public function show(Request $request)
    {
        abort_unless($this->pending($request), 404);

        return view('admin.two-factor');
    }

    public function verify(Request $request, Google2FA $google2fa, AuthController $auth)
    {
        $pending = $this->pending($request);
        abort_unless($pending, 404);

        $request->validate(['code' => 'required|string|max:20']);

        $key = '2fa:'.$pending['id'].'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['code' => 'Too many attempts. Sign in again in a minute.']);
        }

        $admin = Superadmin::findOrFail($pending['id']);
        $code = trim($request->input('code'));

        $valid = preg_match('/^\d{6}$/', $code)
            ? $google2fa->verifyKey($admin->two_factor_secret, $code, 1)
            : $admin->consumeRecoveryCode($code);

        if (! $valid) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages(['code' => 'That code is not right.']);
        }

        RateLimiter::clear($key);
        $request->session()->forget('admin_2fa');

        return $auth->complete($request, $admin, $pending['remember']);
    }

    private function pending(Request $request): ?array
    {
        $pending = $request->session()->get('admin_2fa');

        return $pending && now()->timestamp - $pending['at'] < 600 ? $pending : null;
    }
}
