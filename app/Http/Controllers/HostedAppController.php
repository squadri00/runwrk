<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HostedAppController extends Controller
{
    private const ICONS = [
        'icon-192.png' => 'default-192.png',
        'icon-512.png' => 'default-512.png',
        'maskable-512.png' => 'default-maskable-512.png',
        'apple-touch-180.png' => 'default-apple-180.png',
    ];

    public function page(Business $business)
    {
        $this->live($business);

        $hex = ltrim($business->theme_color, '#');
        [$r, $g, $b] = array_map('hexdec', str_split(str_pad($hex, 6, '0'), 2));

        return response()->view('hosted.app', [
            'business' => $business,
            'logo' => $business->logo_path ? Storage::disk('public')->url($business->logo_path) : null,
            'onTheme' => ($r * 299 + $g * 587 + $b * 114) / 1000 > 150 ? '#111827' : '#ffffff',
            'mapUrl' => $business->address ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($business->address) : null,
            'vapid' => config('runwrk.vapid.public'),
        ]);
    }

    public function manifest(Business $business)
    {
        $this->live($business);

        $base = '/'.$business->slug.'/';
        $icon = fn (string $file, string $size, string $purpose = 'any') => ['src' => $base.'icons/'.$file, 'sizes' => $size, 'type' => 'image/png', 'purpose' => $purpose];

        return response()->json([
            'id' => $base,
            'name' => $business->name,
            'short_name' => $business->short_name ?: Str::limit($business->name, 12, ''),
            'start_url' => $base,
            'scope' => $base,
            'display' => 'standalone',
            'orientation' => 'portrait',
            'theme_color' => $business->theme_color,
            'background_color' => $business->background_color,
            'icons' => [$icon('icon-192.png', '192x192'), $icon('icon-512.png', '512x512'), $icon('maskable-512.png', '512x512', 'maskable')],
        ], 200, ['Content-Type' => 'application/manifest+json', 'Cache-Control' => 'public, max-age=300']);
    }

    public function serviceWorker(Business $business)
    {
        $this->live($business);

        return response("importScripts('/assets/sw-core.js');\n", 200, ['Content-Type' => 'application/javascript', 'Cache-Control' => 'no-cache']);
    }

    public function icon(Business $business, string $file)
    {
        $this->live($business);
        abort_unless(isset(self::ICONS[$file]), 404);

        $path = $business->icon_path ? $business->icon_path.'/'.$file : null;

        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path, null, ['Cache-Control' => 'public, max-age=3600']);
        }

        return response()->file(public_path('assets/icons/'.self::ICONS[$file]), ['Cache-Control' => 'public, max-age=3600']);
    }

    private function live(Business $business): void
    {
        abort_unless($business->isLive(), 404);
    }
}
