# Local phone testing: starts a Cloudflare quick tunnel and the app, then prints the https address.
# Usage (PowerShell, from the project folder):  .\scripts\dev-phone.ps1
# Stop with Ctrl+C. The tunnel address changes every run, so APP_URL in .env is updated each time.
$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)

$php = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $php) { $php = 'D:\xampp\php\php.exe' }
$env:OPENSSL_CONF = Join-Path (Split-Path $php -Parent) 'extras\ssl\openssl.cnf'

$cloudflared = (Get-Command cloudflared -ErrorAction SilentlyContinue).Source
if (-not $cloudflared) { $cloudflared = 'C:\Program Files (x86)\cloudflared\cloudflared.exe' }

$log = Join-Path $env:TEMP 'cloudflared-runwrk.log'
Set-Content $log ''
$tunnel = Start-Process -FilePath $cloudflared -ArgumentList 'tunnel', '--url', 'http://127.0.0.1:8123', '--no-autoupdate', '--logfile', $log -PassThru -WindowStyle Hidden

try {
    $url = $null
    for ($i = 0; $i -lt 40 -and -not $url; $i++) {
        Start-Sleep 1
        $m = Select-String -Path $log -Pattern 'https://[a-z0-9-]+\.trycloudflare\.com' | Select-Object -First 1
        if ($m) { $url = $m.Matches[0].Value }
    }
    if (-not $url) { throw 'Could not get a tunnel address. See ' + $log }

    (Get-Content .env) -replace '^APP_URL=.*', "APP_URL=$url" | Set-Content .env
    Write-Host "`nOpen on your phone:  $url/demo-barber`nAdmin on your computer: $url/login`n" -ForegroundColor Green
    & $php artisan serve --port=8123
}
finally {
    Stop-Process -Id $tunnel.Id -Force -ErrorAction SilentlyContinue
    (Get-Content .env) -replace '^APP_URL=.*', 'APP_URL=http://localhost:8123' | Set-Content .env
}
