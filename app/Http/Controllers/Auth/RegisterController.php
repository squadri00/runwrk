<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Ops\PlatformSettings;
use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Models\PendingRegistration;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Signup stashes the form as a PendingRegistration; the real business and owner
 * are only created once the emailed code is confirmed. Online payment is added
 * in the billing phase; until then every plan confirms by code and billing is
 * followed up by hand.
 */
class RegisterController extends Controller
{
    public function show(Request $request)
    {
        abort_unless(PlatformSettings::signupsEnabled(), 404);

        return view('auth.register', ['plan' => $this->resolvePlan($request->query('plan'))]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(PlatformSettings::signupsEnabled(), 404);

        $plan = $this->resolvePlan($request->input('plan'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'business_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        PendingRegistration::where('email', $data['email'])->whereNull('finalized_at')->delete();

        $pending = PendingRegistration::create([
            'name' => $data['name'],
            'business_name' => $data['business_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'plan_id' => $plan->id,
        ]);

        try {
            Mail::to($pending->email)->send(new RegistrationOtpMail($pending->name, $pending->issueOtp()));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('register.verify', $pending->token);
    }

    private function resolvePlan(?string $code): Plan
    {
        return Plan::public()->where('code', $code)->first()
            ?? Plan::public()->where('price_cents', 0)->first()
            ?? Plan::public()->firstOrFail();
    }
}
