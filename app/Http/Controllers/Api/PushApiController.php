<?php

namespace App\Http\Controllers\Api;

use App\Domain\Api\OriginChecker;
use App\Domain\Tenancy\CurrentBusiness;
use App\Http\Controllers\Controller;
use App\Models\PushMessage;
use App\Models\PushSubscription;
use App\Services\ManifestBuilder;
use Illuminate\Http\Request;

class PushApiController extends Controller
{
    public function config(CurrentBusiness $current, ManifestBuilder $manifests)
    {
        $business = $current->getOrFail();
        $base = rtrim(config('app.url'), '/');

        return [
            'vapidPublicKey' => config('runwrk.vapid.public'),
            'name' => $business->name,
            'shortName' => $business->short_name ?: $business->name,
            'themeColor' => $business->theme_color,
            'backgroundColor' => $business->background_color,
            'icon' => $manifests->iconBase($business).'icon-192.png',
            'appUrl' => $base.'/'.$business->slug,
        ];
    }

    /** Web app manifest for the business's own website (fetched by the Grav plugin, or built into a file). */
    public function manifest(Request $request, CurrentBusiness $current, OriginChecker $origins, ManifestBuilder $manifests)
    {
        $site = (string) $request->query('site');
        $scheme = strtolower((string) parse_url($site, PHP_URL_SCHEME));
        $business = $current->getOrFail();

        if (! in_array($scheme, ['http', 'https'], true) || ! $origins->host($site) || ! $origins->check($business, $site)) {
            return response()->json(['error' => 'site_not_allowed'], 403);
        }

        return response()->json($manifests->forSite($business, $site), 200, ['Content-Type' => 'application/manifest+json', 'Cache-Control' => 'public, max-age=300']);
    }

    public function subscribe(Request $request, CurrentBusiness $current)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url:https', 'max:2000', fn ($a, $v, $fail) => $this->pushHost($v) ?: $fail('Unsupported push service.')],
            'keys.p256dh' => 'required|string|min:80|max:100|regex:/^[A-Za-z0-9_\-=]+$/',
            'keys.auth' => 'required|string|min:16|max:40|regex:/^[A-Za-z0-9_\-=]+$/',
            'contentEncoding' => 'nullable|in:aesgcm,aes128gcm',
            'source' => 'nullable|in:hosted,grav,snippet',
        ]);

        $business = $current->getOrFail();
        $hash = PushSubscription::hashEndpoint($data['endpoint']);

        $existing = PushSubscription::withoutBusinessScope()->where('endpoint_hash', $hash)->first();

        if ($existing && $existing->business_id !== $business->id) {
            $existing->delete();
            $existing = null;
        }

        $attributes = [
            'endpoint' => $data['endpoint'], 'p256dh' => $data['keys']['p256dh'], 'auth' => $data['keys']['auth'],
            'content_encoding' => $data['contentEncoding'] ?? 'aes128gcm', 'source' => $data['source'] ?? 'hosted',
            'platform' => PushSubscription::platformFromUserAgent($request->userAgent()),
            'origin' => substr((string) $request->headers->get('Origin'), 0, 255) ?: null,
            'user_agent' => substr((string) $request->userAgent(), 0, 255), 'fail_count' => 0,
        ];

        $subscription = $existing ?? new PushSubscription(['business_id' => $business->id, 'endpoint_hash' => $hash]);
        $subscription->fill($attributes)->save();

        return response()->json(['ok' => true], $existing ? 200 : 201);
    }

    public function unsubscribe(Request $request)
    {
        $data = $request->validate(['endpoint' => 'required|url|max:2000']);

        PushSubscription::where('endpoint_hash', PushSubscription::hashEndpoint($data['endpoint']))->delete();

        return ['ok' => true];
    }

    public function click(Request $request)
    {
        $data = $request->validate(['message' => 'required|integer']);

        PushMessage::whereKey($data['message'])->increment('click_count');

        return ['ok' => true];
    }

    private function pushHost(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        foreach (config('runwrk.push.allowed_hosts') as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.'.$allowed)) {
                return true;
            }
        }

        return false;
    }
}
