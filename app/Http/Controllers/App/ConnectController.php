<?php

namespace App\Http\Controllers\App;

use App\Domain\Tenancy\CurrentBusiness;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\ManifestBuilder;
use App\Support\Qr;
use Illuminate\Http\Request;
use ZipArchive;

class ConnectController extends Controller
{
    public function index(Request $request, CurrentBusiness $current)
    {
        $business = $current->getOrFail()->load('domains');
        $sites = $this->sites($business);
        $site = $this->chosen($request, $sites);
        $base = rtrim(config('app.url'), '/');

        return view('app.connect', [
            'business' => $business,
            'sites' => $sites,
            'site' => $site,
            'appUrl' => $base.'/'.$business->slug,
            'qr' => Qr::svg($base.'/'.$business->slug, 170),
            'snippet' => '<script src="'.$base.'/assets/runwrk-connect.js" data-key="'.$business->public_key.'" async></script>',
        ]);
    }

    public function worker(ManifestBuilder $manifests)
    {
        return response($manifests->workerScript(), 200, [
            'Content-Type' => 'application/javascript',
            'Content-Disposition' => 'attachment; filename="runwrk-sw.js"',
        ]);
    }

    public function manifest(Request $request, CurrentBusiness $current, ManifestBuilder $manifests)
    {
        $business = $current->getOrFail()->load('domains');
        $site = $this->chosen($request, $this->sites($business));

        abort_unless($site, 422, 'Add your website address first.');

        return response(json_encode($manifests->forSite($business, $site), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), 200, [
            'Content-Type' => 'application/manifest+json',
            'Content-Disposition' => 'attachment; filename="runwrk-manifest.webmanifest"',
        ]);
    }

    /** The Grav plugin, zipped with this business's key already filled in. */
    public function gravPlugin(CurrentBusiness $current)
    {
        $business = $current->getOrFail();
        $dir = base_path('integrations/grav/runwrk-connect');
        $tmp = tempnam(sys_get_temp_dir(), 'rw');
        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::OVERWRITE);

        foreach (glob($dir.'/*') as $file) {
            $name = basename($file);
            $body = $name === 'runwrk-connect.yaml'
                ? "enabled: true\nkey: '{$business->public_key}'\napi_url: '".rtrim(config('app.url'), '/')."'\nposition: right\n"
                : file_get_contents($file);
            $zip->addFromString('runwrk-connect/'.$name, $body);
        }

        $zip->close();

        return response()->download($tmp, 'runwrk-connect.zip', ['Content-Type' => 'application/zip'])->deleteFileAfterSend(true);
    }

    /** @return array<int, string> website addresses this business may use (no wildcards) */
    private function sites(Business $business): array
    {
        $sites = $business->domains->pluck('domain')->reject(fn ($d) => str_starts_with($d, '*.'))->map(fn ($d) => 'https://'.$d)->values()->all();

        if (! $sites && $business->website_url) {
            $url = parse_url($business->website_url);
            $sites[] = ($url['scheme'] ?? 'https').'://'.($url['host'] ?? '');
        }

        return array_values(array_filter(array_unique($sites)));
    }

    private function chosen(Request $request, array $sites): ?string
    {
        $wanted = (string) $request->query('site');

        return in_array($wanted, $sites, true) ? $wanted : ($sites[0] ?? null);
    }
}
