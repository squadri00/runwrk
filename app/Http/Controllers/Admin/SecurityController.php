<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ops\Audit;
use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class SecurityController extends Controller
{
    public function show(Request $request, Google2FA $google2fa)
    {
        $admin = Auth::guard('superadmin')->user();

        if ($admin->hasTwoFactor()) {
            return view('admin.security', ['enabled' => true]);
        }

        $secret = $request->session()->get('admin_2fa_setup') ?? tap($google2fa->generateSecretKey(), fn ($s) => $request->session()->put('admin_2fa_setup', $s));
        $url = $google2fa->getQRCodeUrl(config('app.name').' Admin', $admin->email, $secret);
        $qr = (new Writer(new ImageRenderer(new RendererStyle(200, 1), new SvgImageBackEnd())))->writeString($url);

        return view('admin.security', ['enabled' => false, 'secret' => $secret, 'qr' => $qr]);
    }

    public function enable(Request $request, Google2FA $google2fa)
    {
        $request->validate(['code' => 'required|digits:6']);

        $secret = $request->session()->get('admin_2fa_setup');
        abort_unless($secret, 422);

        if (! $google2fa->verifyKey($secret, $request->input('code'), 1)) {
            return back()->withErrors(['code' => 'That code is not right. Check your phone clock and try again.']);
        }

        $admin = Auth::guard('superadmin')->user();
        $admin->forceFill(['two_factor_secret' => $secret, 'two_factor_confirmed_at' => now()])->save();
        $codes = $admin->issueRecoveryCodes();
        $request->session()->forget('admin_2fa_setup');
        Audit::log('superadmin.2fa_enabled');

        return redirect()->route('admin.security')->with('recovery_codes', $codes)->with('status', 'Two-factor authentication is on.');
    }

    public function regenerate(Request $request)
    {
        $admin = $this->confirmed($request);
        $codes = $admin->issueRecoveryCodes();
        Audit::log('superadmin.2fa_codes_regenerated');

        return redirect()->route('admin.security')->with('recovery_codes', $codes)->with('status', 'New recovery codes generated. The old ones no longer work.');
    }

    public function disable(Request $request)
    {
        $admin = $this->confirmed($request);
        $admin->forceFill(['two_factor_secret' => null, 'two_factor_recovery_codes' => null, 'two_factor_confirmed_at' => null])->save();
        Audit::log('superadmin.2fa_disabled');

        return redirect()->route('admin.security')->with('status', 'Two-factor authentication is off.');
    }

    private function confirmed(Request $request)
    {
        $request->validate(['password' => 'required']);
        $admin = Auth::guard('superadmin')->user();

        if (! Hash::check($request->input('password'), $admin->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['password' => 'Wrong password.']);
        }

        return $admin;
    }
}
