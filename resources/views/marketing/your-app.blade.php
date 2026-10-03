<x-layouts.marketing title="Your Own App for Your Business" description="Get your own app for your business. Your customers add it to their phone, and you can send a message to all of them any time. No app store needed.">

    <section class="rw-banner">
        <div class="container">
            <h1>Your Own App</h1>
            <p>Send a message to all your customers' phones, any time you like.</p>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="row align-items-center gy-5 gx-lg-5">
                <div class="col-lg-6">
                    <div class="rw-head text-start mx-0 mb-3"><h2>A small app with your name and logo</h2></div>
                    <p>Your customers add it to their phone in a few taps. It opens like any other app and sits on their home screen with your logo. You do not need the app store, and nobody has to download anything.</p>
                    <p>The best part: you can send them a message whenever you want. It pops up right on their phone screen.</p>
                    <ul class="rw-list mt-3">
                        <li><b>Your logo and colours</b> on their home screen</li>
                        <li><b>Messages that get seen</b>, right on the phone screen</li>
                        <li><b>Works on iPhone and Android</b></li>
                    </ul>
                    <a class="btn_one" href="{{ route('demo') }}">Try the demo on your phone</a>
                </div>
                <div class="col-lg-6">@include('marketing._phone')</div>
            </div>
        </div>
    </section>

    <section class="rw-section soft">
        <div class="container">
            <div class="rw-head"><h2>How it works</h2><p>Easy for you, easy for your customers.</p></div>
            <div class="row g-4 rw-steps">
                <div class="col-md-4 step"><div class="no">1</div><h3>Customers open your link</h3><p>Share it on your website, a poster, a card or a QR code at your counter.</p></div>
                <div class="col-md-4 step"><div class="no">2</div><h3>They add it to their phone</h3><p>They tap "Add to home screen" and then "Turn on notifications". We show them how.</p></div>
                <div class="col-md-4 step"><div class="no">3</div><h3>You send a message</h3><p>Type it in your dashboard, press send, and it appears on their phones in seconds.</p></div>
            </div>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="rw-head"><h2>What could you send?</h2><p>A few ideas that work well for small businesses.</p></div>
            <div class="row g-4">
                @foreach ([
                    ['ti-gift', "Today's special", 'Fill your quiet hours with a deal your regulars will love.'],
                    ['ti-alarm-clock', 'Reminders', 'Remind customers about appointments, events or opening times.'],
                    ['ti-bolt', 'Last-minute openings', '"Two slots free at 4pm." Fill a cancelled spot fast.'],
                    ['ti-package', 'New things', 'Tell everyone about a new product, service or event.'],
                ] as [$icon, $h, $p])
                    <div class="col-sm-6 col-lg-3"><div class="rw-card small"><div class="ico"><span class="{{ $icon }}"></span></div><h3>{{ $h }}</h3><p class="mb-0">{{ $p }}</p></div></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="rw-section soft">
        <div class="container">
            <div class="row gy-5 gx-lg-5">
                <div class="col-lg-6">
                    <div class="rw-head text-start mx-0 mb-3"><h2>What you get</h2></div>
                    <ul class="rw-list">
                        <li><b>Your own app page</b> with your logo, colours, phone number and directions</li>
                        <li><b>Send now or schedule</b> for later</li>
                        <li><b>Add a picture</b> to your message</li>
                        <li><b>See the results</b>: how many got it and how many tapped it</li>
                        <li><b>Your team can send too</b> with their own logins</li>
                        <li><b>Works with your website</b> or all on its own</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="rw-card">
                        <h3>Your customers stay in control</h3>
                        <p>Customers choose to turn messages on, and they can turn them off in the app at any time. No one gets messages they did not ask for.</p>
                        <h3 class="mt-4">A note about iPhone</h3>
                        <p class="mb-0">On an iPhone, customers first add the app to their home screen, then turn on messages. Your app page shows them each step.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-cta-band">
        <div class="container">
            <h2>Want your own app?</h2>
            <p class="mb-4">@if ($fromPrice)Plans start at {{ $fromPrice->priceLabel() }} a {{ $fromPrice->interval }}. @endif See the plans or talk to us.</p>
            <a class="btn_one me-2" href="{{ route('pricing') }}">See prices</a>
            <a class="btn_line" href="{{ route('contact') }}">Contact us</a>
        </div>
    </section>
</x-layouts.marketing>
