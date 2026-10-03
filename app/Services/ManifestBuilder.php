<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Str;

class ManifestBuilder
{
    public function iconBase(Business $business): string
    {
        return rtrim(config('app.url'), '/').'/'.$business->slug.'/icons/';
    }

    /** Manifest for a business's own website. `$site` is the site address, e.g. https://joes.com or http://localhost/grav */
    public function forSite(Business $business, string $site): array
    {
        $path = '/'.trim((string) parse_url($site, PHP_URL_PATH), '/');
        $scope = $path === '/' ? '/' : $path.'/';
        $icons = $this->iconBase($business);
        $icon = fn (string $file, string $size, string $purpose = 'any') => ['src' => $icons.$file, 'sizes' => $size, 'type' => 'image/png', 'purpose' => $purpose];

        return [
            'id' => $scope,
            'name' => $business->name,
            'short_name' => $business->short_name ?: Str::limit($business->name, 12, ''),
            'start_url' => $scope,
            'scope' => $scope,
            'display' => 'standalone',
            'orientation' => 'portrait',
            'theme_color' => $business->theme_color,
            'background_color' => $business->background_color,
            'icons' => [$icon('icon-192.png', '192x192'), $icon('icon-512.png', '512x512'), $icon('maskable-512.png', '512x512', 'maskable')],
        ];
    }

    /** The one-line worker file a connected website serves from its own address. */
    public function workerScript(): string
    {
        return "importScripts('".rtrim(config('app.url'), '/')."/assets/sw-core.js');\n";
    }
}
