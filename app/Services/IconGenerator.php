<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Facades\Storage;

class IconGenerator
{
    private const SETS = [
        'icon-192.png' => [192, 0.8],
        'icon-512.png' => [512, 0.8],
        'maskable-512.png' => [512, 0.6],
        'apple-touch-180.png' => [180, 0.8],
    ];

    public function iconDir(Business $business): string
    {
        return "businesses/{$business->id}/icons";
    }

    /** Builds the home-screen icons from the stored logo, on the business background colour. */
    public function generate(Business $business): bool
    {
        if (! $business->logo_path || ! Storage::disk('public')->exists($business->logo_path)) {
            return false;
        }

        $logo = @imagecreatefromstring(Storage::disk('public')->get($business->logo_path));

        if (! $logo) {
            return false;
        }

        [$r, $g, $b] = sscanf($business->background_color ?: '#ffffff', '#%02x%02x%02x');
        $lw = imagesx($logo);
        $lh = imagesy($logo);

        foreach (self::SETS as $file => [$size, $fill]) {
            $canvas = imagecreatetruecolor($size, $size);
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, $r ?? 255, $g ?? 255, $b ?? 255));

            $box = (int) round($size * $fill);
            $scale = min($box / $lw, $box / $lh);
            $w = max(1, (int) round($lw * $scale));
            $h = max(1, (int) round($lh * $scale));
            imagecopyresampled($canvas, $logo, intdiv($size - $w, 2), intdiv($size - $h, 2), 0, 0, $w, $h, $lw, $lh);

            ob_start();
            imagepng($canvas);
            Storage::disk('public')->put($this->iconDir($business).'/'.$file, ob_get_clean());
        }

        $business->forceFill(['icon_path' => $this->iconDir($business)])->save();

        return true;
    }

    public function purge(Business $business): void
    {
        Storage::disk('public')->deleteDirectory("businesses/{$business->id}");
        $business->forceFill(['logo_path' => null, 'icon_path' => null])->save();
    }
}
