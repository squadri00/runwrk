<x-layouts.marketing title="Terms of Service" description="The simple terms for using Runwrk's website design and app services.">
    <section class="rw-banner"><div class="container"><h1>Terms of Service</h1><p>The simple version.</p></div></section>
    <section class="rw-section">
        <div class="container rw-legal">
            <p><em>Last updated: {{ date('F Y') }}</em></p>
            <p>These terms cover {{ config('app.name') }} services from {{ config('site.parent.name') }}. By ordering a service or signing up, you agree to them.</p>

            <h2>Website design</h2>
            <p>The first-year price covers the design, a domain name, hosting and SSL as described on our <a href="{{ route('web-design') }}">Web Design</a> page. You provide your content (text, photos and business details). Content writing is optional and charged per page. The design includes 1 or 2 revisions. The timeline starts when we have your content.</p>

            <h2>After the first year</h2>
            <p>You can continue with our optional subscription at the price shown on our website, or take your source code and host it yourself. Domain and hosting renewals are not included after the first year unless you subscribe.</p>

            <h2>Your own app</h2>
            <p>App plans are billed as shown on our <a href="{{ route('pricing') }}">Pricing</a> page. You are responsible for the messages you send. Only send messages to customers who turned them on, and follow the laws that apply to your business, including rules about marketing messages.</p>

            <h2>Your content</h2>
            <p>You own your content. You confirm you have the right to use the text, photos and logos you give us.</p>

            <h2>Acceptable use</h2>
            <p>Do not use our services for anything illegal, misleading or harmful. We may suspend an account that breaks this rule.</p>

            <h2>Changes and contact</h2>
            <p>We may update these terms and will post the new version here. Questions? <a href="{{ route('contact') }}">Contact us</a>.</p>
        </div>
    </section>
</x-layouts.marketing>
