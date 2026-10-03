<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Billing\RegistrationFinalizer;
use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Models\PendingRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegistrationOtpController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $pending = PendingRegistration::where('token', $token)->firstOrFail();

        if ($pending->isFinalized()) {
            return redirect()->route('login')->with('status', 'Your account is ready. Sign in below.');
        }

        return view('auth.verify-otp', ['pending' => $pending]);
    }

    public function verify(Request $request, string $token): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $pending = PendingRegistration::where('token', $token)->firstOrFail();

        if ($pending->isFinalized()) {
            return redirect()->route('login')->with('status', 'Your account is ready. Sign in below.');
        }

        if (! $pending->otpMatches($request->string('code'))) {
            $pending->increment('otp_attempts');

            return back()->withErrors([
                'code' => $pending->otp_attempts >= 5 ? 'Too many attempts. Request a new code.' : 'That code is not right or has expired.',
            ]);
        }

        $business = app(RegistrationFinalizer::class)->finalize($pending);

        Auth::guard('web')->login($business->owner());
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Welcome! Your account is ready.');
    }

    public function resend(string $token): RedirectResponse
    {
        $pending = PendingRegistration::where('token', $token)->firstOrFail();

        if ($pending->isFinalized()) {
            return redirect()->route('login');
        }

        try {
            Mail::to($pending->email)->send(new RegistrationOtpMail($pending->name, $pending->issueOtp()));
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', "We couldn't send the code just now. Try again in a moment.");
        }

        return back()->with('status', 'A new code is on its way.');
    }
}
