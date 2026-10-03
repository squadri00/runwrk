<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ops\Audit;
use App\Domain\Ops\PlatformSettings;
use App\Domain\Ops\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['s' => PlatformSettings::all() ?: ['mail' => [], 'mail_password_set' => false]]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'nullable|string|max:60',
            'support_email' => 'nullable|email|max:190',
            'mail_host' => 'nullable|string|max:190',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|in:tls,ssl,none',
            'mail_username' => 'nullable|string|max:190',
            'mail_password' => 'nullable|string|max:190',
            'mail_from_address' => 'nullable|email|max:190',
            'mail_from_name' => 'nullable|string|max:120',
        ]);

        Settings::put('app_name', $data['app_name'] ?? null);
        Settings::put('support_email', $data['support_email'] ?? null);
        Settings::put('signups_enabled', $request->boolean('signups_enabled'));

        foreach (PlatformSettings::MAIL as $key) {
            Settings::put("mail.$key", $data["mail_$key"] ?? null);
        }

        if (filled($data['mail_password'] ?? null)) {
            Settings::putSecret('mail.password', $data['mail_password']);
        }

        PlatformSettings::forget();
        Audit::log('settings.update', null, ['mail_host' => $data['mail_host'] ?? null, 'signups_enabled' => $request->boolean('signups_enabled')]);

        return redirect()->route('admin.settings.edit')->with('status', 'Settings saved.');
    }

    public function testMail()
    {
        $to = Auth::guard('superadmin')->user()->email;

        try {
            Mail::purge();
            Mail::raw('This is a test message from '.config('app.name').'. Your mail settings work.', fn ($m) => $m->to($to)->subject('Test email'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Sending failed: '.$e->getMessage());
        }

        return back()->with('status', 'Test email sent to '.$to.' (check the log if the mailer is "log").');
    }
}
