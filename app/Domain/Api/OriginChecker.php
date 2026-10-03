<?php

namespace App\Domain\Api;

use App\Models\Business;

/**
 * A request with no Origin/Referer is not a cross-origin browser request, so it
 * passes. A browser request must come from one of the business's allowed domains.
 * An empty allow-list means nothing is allowed (except localhost in local dev).
 */
class OriginChecker
{
    public function check(Business $business, ?string $origin, ?string $referer = null): bool
    {
        $host = $this->host($origin) ?? $this->host($referer);

        if ($host === null) {
            return true;
        }

        if (config('runwrk.allow_local_origins') && $this->isLocal($host)) {
            return true;
        }

        return $business->domains()->pluck('domain')->contains(fn ($d) => $this->matches($host, $d));
    }

    public function host(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);
        if (! str_contains($url, '://')) {
            $url = 'https://'.$url;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host ? strtolower($host) : null;
    }

    public function normalize(string $input): string
    {
        $input = trim(strtolower($input));
        if (str_starts_with($input, '*.')) {
            return '*.'.$this->host(substr($input, 2));
        }

        return (string) $this->host($input);
    }

    private function isLocal(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1', '::1', '[::1]'], true)
            || str_ends_with($host, '.localhost')
            || str_ends_with($host, '.test');
    }

    private function matches(string $host, string $pattern): bool
    {
        if (str_starts_with($pattern, '*.')) {
            $base = substr($pattern, 2);

            return $host === $base || str_ends_with($host, '.'.$base);
        }

        return $host === $pattern;
    }
}
