@php($w = config('site.website'))
<x-layouts.marketing title="Pricing" description="Simple prices. A complete website for ${{ $w['price'] }} the first year (regular ${{ $w['original_price'] }}), then {{ $w['renewal'] }} if you want to keep going. Your own app plans too.">

    <section class="rw-banner">
        <div class="container">
            <h1>Pricing</h1>
            <p>Simple prices. No hidden fees.</p>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="rw-head"><h2>Website</h2><p>A complete website for your business, done for you.</p></div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="rw-offer">
                        <span class="tag">Limited offer</span>
                        <div class="was">${{ $w['original_price'] }}</div>
                        <div class="now">${{ $w['price'] }}</div>
                        <div class="sub">for your first year</div>
                        <ul class="rw-list text-start d-inline-block">
                            <li>Complete website design, 1 or 2 free revisions</li>
                            <li>Free domain name, hosting and SSL</li>
                            <li>Easy back end to edit your own site</li>
                            <li>Search-friendly setup, built to be fast</li>
                            <li>Galleries, accordions, cards, counters and more</li>
                            <li>Contact form, social links and Google Analytics</li>
                            <li>Light and dark mode</li>
                            <li>Complete source code is yours</li>
                            <li>Ready in {{ $w['turnaround'] }}</li>
                        </ul>
                        <div><a class="btn_one d-inline-block" href="{{ route('contact') }}">Get started</a></div>
                        <div class="after"><b>After the first year:</b> optional subscription of {{ $w['renewal'] }} for hosting, domain, SSL and support.<br>
                            <b>Need help with words?</b> We can write your content for ${{ $w['content_per_page'] }} a page.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-section soft rw-plans">
        <div class="container">
            <div class="rw-head"><h2>Your own app</h2><p>Send messages to all your customers' phones. Add it to your website, or use it on its own.</p></div>
            @if ($plans->isEmpty())
                <p class="text-center">Plans are coming soon. <a href="{{ route('contact') }}">Contact us</a> and we will set you up.</p>
            @else
                @php($cols = [1 => 'col-lg-6', 2 => 'col-lg-6', 3 => 'col-lg-4', 4 => 'col-lg-3'][min($plans->count(), 4)])
                <div class="row text-center justify-content-center">
                    @foreach ($plans as $plan)
                        <div class="{{ $cols }} col-sm-6">
                            <div class="single-pricing {{ $loop->index === 1 && $plans->count() > 2 ? 'single-pricing-white' : '' }}">
                                <div class="price-head"><h2>{{ $plan->name }}</h2><span></span><span></span><span></span><span></span><span></span><span></span></div>
                                <div class="price">{{ $plan->priceLabel() }}</div>
                                <h5>{{ $plan->isFree() ? 'To try it out' : ($plan->interval === 'year' ? 'Yearly' : 'Monthly') }}</h5>
                                @if ($plan->description)<p class="small-note px-3">{{ $plan->description }}</p>@endif
                                <ul>@foreach ($plan->features ?? [] as $f)<li>{{ $f }}</li>@endforeach</ul>
                                @if ($signups)
                                    <a class="btn_one" href="{{ route('register', ['plan' => $plan->code]) }}">{{ $plan->isFree() ? 'Start free' : 'Get started' }}</a>
                                @else
                                    <a class="btn_one" href="{{ route('contact') }}">Contact us</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="rw-section">
        <div class="container" style="max-width: 820px">
            <div class="rw-head"><h2>Common questions</h2></div>
            @include('marketing._faq', ['id' => 'pricefaq', 'items' => [
                ['What does the first-year price include?', 'Your complete website design, a free domain name, hosting, SSL, the easy back end, SEO setup, a contact form, social links, Google Analytics, light and dark mode, and your source code. Nothing else to pay in year one.'],
                ['What do I need to give you?', 'Your business details, the text for your pages, and your photos and logo. If you would rather not write the text, we can do it for $'.$w['content_per_page'].' a page.'],
                ['How many changes can I ask for?', '1 or 2 free revisions while we build it. After that, you can make changes yourself in the back end whenever you like.'],
                ['What happens after year one?', 'You can keep going with an optional subscription of '.$w['renewal'].'. It covers hosting, your domain, SSL and support. You also own your source code.'],
                ['How long does it take?', 'About 1 week once we have your content.'],
                ['Can I get the website and the app?', 'Yes. They work well together. Tell us what you need on the contact page and we will put a package together.'],
                ['Do my customers pay anything for the app?', 'No. It is free for them. They choose to turn messages on and can turn them off any time.'],
            ]])
        </div>
    </section>

    <section class="rw-cta-band">
        <div class="container">
            <h2>Not sure what you need?</h2>
            <p class="mb-4">Tell us about your business and we will point you in the right direction.</p>
            <a class="btn_one" href="{{ route('contact') }}">Contact us</a>
        </div>
    </section>
</x-layouts.marketing>
