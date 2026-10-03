<?php

namespace App\Domain\Push;

use App\Models\Business;
use App\Models\PushMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Payload
{
    public static function build(PushMessage $message, Business $business): array
    {
        $base = rtrim(config('app.url'), '/');
        $icon = $business->icon_path
            ? Storage::disk('public')->url($business->icon_path.'/icon-192.png')
            : $base.'/assets/icons/default-192.png';

        return array_filter([
            'title' => Str::limit($message->title, 79, '…'),
            'body' => Str::limit($message->body, 239, '…'),
            'icon' => self::absolute($icon, $base),
            'badge' => $base.'/assets/icons/badge.png',
            'image' => $message->image_path ? self::absolute(Storage::disk('public')->url($message->image_path), $base) : null,
            'url' => $message->url ?: ($business->website_url ?: $base.'/'.$business->slug),
            'tag' => 'm'.$message->id,
            'msg' => $message->id,
            'api' => $base.'/api/v1',
            'key' => $business->public_key,
        ]);
    }

    private static function absolute(string $url, string $base): string
    {
        return str_starts_with($url, 'http') ? $url : $base.'/'.ltrim($url, '/');
    }
}
