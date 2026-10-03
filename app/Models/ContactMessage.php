<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'service', 'message', 'ip', 'user_agent', 'read_at'])]
class ContactMessage extends Model
{
    public const SERVICES = ['website' => 'A website', 'app' => 'Your own app', 'both' => 'Both', 'other' => 'Something else'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }
}
