<?php

namespace App\Http\Controllers\App;

use App\Domain\Ops\Audit;
use App\Domain\Tenancy\CurrentBusiness;
use App\Http\Controllers\Controller;
use App\Services\IconGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function edit(CurrentBusiness $current)
    {
        return view('app.branding', ['business' => $current->getOrFail()->load('domains')]);
    }

    public function update(Request $request, CurrentBusiness $current, IconGenerator $icons)
    {
        $business = $current->getOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'short_name' => 'required|string|max:30',
            'theme_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'phone' => 'nullable|string|max:40',
            'address' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'timezone' => 'required|timezone',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048|dimensions:min_width=256,min_height=256',
        ], ['logo.dimensions' => 'The logo must be at least 256 × 256 pixels so the app icon looks sharp.']);

        $business->fill(collect($data)->except('logo')->all())->save();
        $changes = $business->getChanges();

        if ($request->boolean('remove_logo')) {
            $icons->purge($business);
        }

        if ($request->hasFile('logo')) {
            Storage::disk('public')->deleteDirectory("businesses/{$business->id}");
            $file = $request->file('logo');
            $business->forceFill(['logo_path' => $file->storeAs("businesses/{$business->id}", 'logo.'.$file->extension(), 'public')])->save();
        }

        if ($business->logo_path && ($request->hasFile('logo') || array_key_exists('background_color', $changes))) {
            $icons->generate($business);
        }

        Audit::log('branding.update', $business, array_diff_key($changes, ['updated_at' => 1]) + ($request->hasFile('logo') ? ['logo' => 'replaced'] : []));

        return redirect()->route('branding.edit')->with('status', 'Branding saved.');
    }
}
