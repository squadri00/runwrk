@php($w = config('site.website'))
<x-layouts.marketing title="Web Design for Small Business" description="A complete, modern website for your small business: ${{ $w['price'] }} for the first year with free domain, hosting and SSL. Easy back end, built to be fast, ready in about 1 week.">

    <section class="rw-banner">
        <div class="container">
            <h1>Web Design</h1>
            <p>A complete website for your business, ready in {{ $w['turnaround'] }}. You can change it yourself.</p>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="row justify-content-center gy-5 gx-lg-5 align-items-center">
                <div class="col-lg-5">
                    <div class="rw-offer">
                        <span class="tag">Limited offer</span>
                        <div class="was">${{ $w['original_price'] }}</div>
                        <div class="now">${{ $w['price'] }}</div>
                        <div class="sub">for your first year, everything included</div>
                        <a class="btn_one d-inline-block" href="{{ route('contact') }}">Get started</a>
                        <div class="after">After year one, an optional subscription of <b>{{ $w['renewal'] }}</b> keeps your hosting, domain, SSL and support going.</div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="rw-head text-start mx-0 mb-3"><h2>A real website. A fair price.</h2></div>
                    <p>Most agencies charge thousands for a simple website. We give you a complete, modern one for ${{ $w['price'] }} the first year, with everything you need to get online and look professional.</p>
                    <p>You give us your content. We do the design, and you get 1 or 2 free revisions to make it right.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-section soft">
        <div class="container">
            <div class="rw-head"><h2>Everything that is included</h2><p>No add-ons and no surprises in your first year.</p></div>
            <div class="rw-check-grid">
                @foreach ([
                    ['Free domain name', 'Your own web address, like yourbusiness.com, free for the first year.'],
                    ['Free hosting', 'We keep your website online. No hosting bill in year one.'],
                    ['Free SSL', 'The padlock in the browser that shows visitors your site is safe.'],
                    ['Easy back end', 'Log in and change your own text, photos and pages.'],
                    ['Search-friendly (SEO)', 'Set up so Google can find and understand your pages.'],
                    ['Built for speed', 'Designed to pass Google\'s speed test, so visitors do not wait.'],
                    ['Works on every screen', 'Looks right on phones, tablets and computers.'],
                    ['Ready-made sections', 'Photo galleries, accordions, cards, counters and lots more to add with a click.'],
                    ['One contact form', 'Customers can message you straight from your site.'],
                    ['Social media links', 'Link to your Facebook, Instagram and more.'],
                    ['Google Analytics', 'We add your tracking code so you can see your visitors.'],
                    ['Light and dark mode', 'Visitors can switch the look to suit their eyes.'],
                    ['Your source code', 'You get the complete code. The website is yours.'],
                    ['1 or 2 free revisions', 'We adjust the design until you are happy.'],
                    ['Ready in '.$w['turnaround'], 'Once we have your content, your site is on its way fast.'],
                ] as [$h, $p])
                    <div class="item"><h4>{{ $h }}</h4><p>{{ $p }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="rw-section">
        <div class="container">
            <div class="row gy-5 gx-lg-5 align-items-center">
                <div class="col-lg-6">
                    <div class="rw-head text-start mx-0 mb-3"><h2>Change things yourself, the easy way</h2></div>
                    <p>Your website comes with a simple back end. No code, no confusing menus. Log in and you can:</p>
                    <ul class="rw-list mt-3">
                        <li><b>Edit the words</b> on any page</li>
                        <li><b>Swap photos</b> and add new ones</li>
                        <li><b>Build photo galleries</b> in a few clicks</li>
                        <li><b>Add new pages</b> whenever you need them</li>
                        <li><b>Drop in ready-made sections</b> like cards, accordions and counters</li>
                        <li><b>Manage your SEO</b>: page titles and descriptions for Google</li>
                    </ul>
                    <p>And more. If you get stuck, we are here to help.</p>
                </div>
                <div class="col-lg-6">
                    <div class="rw-card">
                        <h3>What we need from you</h3>
                        <ul class="rw-list">
                            <li><b>Your business details</b>: name, address, phone, opening hours</li>
                            <li><b>Your content</b>: the text for each page</li>
                            <li><b>Your photos</b>: your logo, product pictures, and anything you want shown</li>
                        </ul>
                        <p class="mb-0"><b>Not good with words?</b> We can write the content for you for ${{ $w['content_per_page'] }} per page.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rw-section soft">
        <div class="container">
            <div class="rw-head"><h2>Your website in about a week</h2><p>Timing starts when we have your content.</p></div>
            <div class="row g-4 rw-steps">
                <div class="col-md-3 step"><div class="no">1</div><h3>You send content</h3><p>Business details, text and photos.</p></div>
                <div class="col-md-3 step"><div class="no">2</div><h3>We build it</h3><p>We design your site and set everything up.</p></div>
                <div class="col-md-3 step"><div class="no">3</div><h3>You review</h3><p>Look it over. 1 or 2 free revisions.</p></div>
                <div class="col-md-3 step"><div class="no">4</div><h3>Launch</h3><p>Your site goes live on your own domain.</p></div>
            </div>
        </div>
    </section>

    <section class="rw-cta-band">
        <div class="container">
            <h2>Get your website for ${{ $w['price'] }}</h2>
            <p class="mb-4">Regular price ${{ $w['original_price'] }}. Tell us about your business to get started.</p>
            <a class="btn_one" href="{{ route('contact') }}">Contact us</a>
        </div>
    </section>
</x-layouts.marketing>
