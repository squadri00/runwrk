@props(['title', 'description', 'noindex' => false])

@php
    $canonical = url(request()->path() === '/' ? '/' : '/'.request()->path());
    $fullTitle = request()->routeIs('home') ? $title : $title.' | '.config('app.name');
    $nav = [['web-design', 'Web Design'], ['your-app', 'Your App'], ['pricing', 'Pricing'], ['demo', 'Demo'], ['contact', 'Contact']];
    $parent = config('site.parent');
    $ld = ['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/'), 'parentOrganization' => ['@type' => 'Organization', 'name' => $parent['name'], 'url' => $parent['url']]];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    @if ($noindex)<meta name="robots" content="noindex">@endif
    <meta name="theme-color" content="#525fe1">
    <script>(function(){var d=document.documentElement,t='light';try{t=localStorage.getItem('rw-site-theme')||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');}catch(e){}d.setAttribute('data-theme',t);d.setAttribute('data-bs-theme',t);})();</script>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta name="twitter:card" content="summary">
    <script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES) !!}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Jost:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/site/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/site/fonts/themify-icons.css">
    <link rel="stylesheet" href="/assets/site/css/style.css">
    <link rel="stylesheet" href="/assets/site/css/runwrk-site.css?v={{ filemtime(public_path('assets/site/css/runwrk-site.css')) }}">
    @stack('head')
</head>
<body>
<header class="rw-nav">
    <div class="container rw-nav-in">
        <a class="rw-logo" href="{{ url('/') }}"><span class="rw-mark">R</span>{{ config('app.name') }}</a>
        <button class="rw-burger" type="button" aria-label="Menu" aria-expanded="false" aria-controls="rw-menu"><span></span><span></span><span></span></button>
        <nav id="rw-menu" class="rw-menu">
            @foreach ($nav as [$route, $label])
                <a class="rw-link {{ request()->routeIs($route) ? 'active' : '' }}" href="{{ route($route) }}">{{ $label }}</a>
            @endforeach
            @auth('web')
                <a class="rw-signin" href="{{ route('dashboard') }}">My dashboard</a>
            @else
                <a class="rw-signin" href="{{ route('login') }}">Sign in</a>
            @endauth
            <a class="btn_one rw-cta" href="{{ route('contact') }}">Get started</a>
            <button class="rw-theme" type="button" aria-label="Switch between light and dark mode" title="Light / dark mode">
                <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M6.3 17.7l-1.4 1.4M19.1 4.9l-1.4 1.4"/></svg>
                <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
            </button>
        </nav>
    </div>
</header>

<main>{{ $slot }}</main>

<footer>
    <div class="footer section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-12 rw-foot-brand">
                    <div class="single_footer">
                        <a class="rw-logo" href="{{ url('/') }}"><span class="rw-mark">R</span>{{ config('app.name') }}</a>
                        <p>Simple websites and your own app for small businesses. Easy to use, fast to launch, fair prices.</p>
                        <div class="rw-parent">{{ config('app.name') }} is a product of <a href="{{ $parent['url'] }}" rel="noopener">{{ $parent['name'] }}</a>.</div>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4 col-6">
                    <div class="single_footer">
                        <h4>Services</h4>
                        <ul>
                            <li><a href="{{ route('web-design') }}">Web Design</a></li>
                            <li><a href="{{ route('your-app') }}">Your Own App</a></li>
                            <li><a href="{{ route('pricing') }}">Pricing</a></li>
                            <li><a href="{{ route('demo') }}">Demo</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4 col-6">
                    <div class="single_footer">
                        <h4>Company</h4>
                        <ul>
                            <li><a href="{{ route('contact') }}">Contact</a></li>
                            <li><a href="{{ route('login') }}">Sign in</a></li>
                            <li><a href="{{ route('privacy') }}">Privacy</a></li>
                            <li><a href="{{ route('terms') }}">Terms</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <div class="single_footer">
                        <h4>Ready to start?</h4>
                        <p>Tell us about your business. We will reply by email.</p>
                        <a class="btn_one" href="{{ route('contact') }}">Contact us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="foot_copy">
        <div class="footer_copyright"><p>&copy; {{ date('Y') }} {{ config('app.name') }}, a product of {{ $parent['name'] }}. All rights reserved.</p></div>
    </div>
</footer>

<button class="rw-top" type="button" aria-label="Back to top" title="Back to top">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

<script src="/assets/site/js/site.js" defer></script>
@stack('scripts')
</body>
</html>
