<?php

namespace Grav\Plugin;

use Grav\Common\HTTP\Response as HttpResponse;
use Grav\Common\Plugin;
use Grav\Framework\Psr7\Response;

/**
 * Runwrk Connect: adds your Runwrk app to this website.
 *  - serves /runwrk-sw.js and /runwrk-manifest.webmanifest from this site's own address
 *  - adds the manifest link and the "Get our app" button to every page
 */
class RunwrkConnectPlugin extends Plugin
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onOutputGenerated' => ['onOutputGenerated', 0],
        ];
    }

    public function onPluginsInitialized(): void
    {
        if ($this->isAdmin() || $this->isCli() || ! $this->ready()) {
            return;
        }

        $path = '/'.trim((string) $this->grav['uri']->path(), '/');

        if ($path === '/runwrk-sw.js') {
            $js = "importScripts('".$this->api().'/assets/sw-core.js'."');\n";
            $this->grav->close(new Response(200, [
                'Content-Type' => 'application/javascript; charset=utf-8',
                'Cache-Control' => 'no-cache',
            ], $js));
        }

        if ($path === '/runwrk-manifest.webmanifest') {
            $json = $this->manifest();
            $this->grav->close(new Response($json ? 200 : 502, [
                'Content-Type' => 'application/manifest+json; charset=utf-8',
                'Cache-Control' => 'public, max-age=300',
            ], $json ?: '{}'));
        }
    }

    public function onOutputGenerated(): void
    {
        if ($this->isAdmin() || $this->isCli() || ! $this->ready()) {
            return;
        }

        $out = (string) $this->grav->output;

        if (stripos($out, '<html') === false || stripos($out, '</body>') === false || str_contains($out, 'runwrk-connect.js')) {
            return;
        }

        $base = rtrim((string) $this->grav['base_url_relative'], '/');
        $h = fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES);
        $position = $this->config()['position'] === 'left' ? 'left' : 'right';

        $script = '<script src="'.$h($this->api().'/assets/runwrk-connect.js').'" data-key="'.$h($this->config()['key']).'" data-source="grav"'
            .' data-position="'.$position.'" data-sw="'.$h($base.'/runwrk-sw.js').'" data-manifest="'.$h($base.'/runwrk-manifest.webmanifest').'" async></script>';

        if (! preg_match('/<link[^>]+rel=["\']manifest["\']/i', $out)) {
            $out = preg_replace('/<\/head>/i', '<link rel="manifest" href="'.$h($base.'/runwrk-manifest.webmanifest').'">'."\n</head>", $out, 1);
        }

        $this->grav->output = preg_replace('/<\/body>/i', $script."\n</body>", $out, 1);
    }

    private function ready(): bool
    {
        return (bool) ($this->config()['enabled'] ?? false) && preg_match('/^pk_[A-Za-z0-9]{20,}$/', (string) ($this->config()['key'] ?? '')) === 1;
    }

    private function api(): string
    {
        return rtrim((string) ($this->config()['api_url'] ?: 'https://runwrk.com'), '/');
    }

    /** The manifest comes from Runwrk but is served from this site's own address, which browsers require. */
    private function manifest(): ?string
    {
        $site = rtrim((string) $this->grav['uri']->rootUrl(true), '/');
        $dir = $this->grav['locator']->findResource('cache://', true).'/runwrk-connect';
        $file = $dir.'/manifest-'.md5($site.$this->config()['key']).'.json';

        if (is_file($file) && filemtime($file) > time() - 3600) {
            return file_get_contents($file);
        }

        try {
            $body = HttpResponse::get($this->api().'/api/v1/manifest?site='.rawurlencode($site), [
                'headers' => ['X-Runwrk-Key' => $this->config()['key'], 'Accept' => 'application/json'],
                'timeout' => 8,
            ]);

            if (is_array(json_decode($body, true)) && isset(json_decode($body, true)['start_url'])) {
                is_dir($dir) || mkdir($dir, 0775, true);
                file_put_contents($file, $body);

                return $body;
            }
        } catch (\Throwable $e) {
            // fall through to the last good copy
        }

        return is_file($file) ? file_get_contents($file) : null;
    }
}
