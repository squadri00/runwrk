<?php

namespace App\Domain\Tenancy;

use App\Mail\InviteMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class Invites
{
    /** Creates a user with an unusable password and returns the set-password link. */
    public function createUser(array $attributes): array
    {
        $user = User::create($attributes + ['password' => Str::random(40)]);

        return [$user, $this->send($user)];
    }

    public function send(User $user): string
    {
        $token = Password::broker()->createToken($user);
        $url = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            Mail::to($user->email)->send(new InviteMail($user->name, $user->business->name, $url));
        } catch (\Throwable $e) {
            report($e);
        }

        return $url;
    }
}
