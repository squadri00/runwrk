<x-layouts.marketing title="Privacy Policy" description="Plain words about what Runwrk collects, why, and the choices you and your customers have about your information." :noindex="false">
    <section class="rw-banner"><div class="container"><h1>Privacy Policy</h1><p>Plain words about your information.</p></div></section>
    <section class="rw-section">
        <div class="container rw-legal">
            <p><em>Last updated: {{ date('F Y') }}</em></p>
            <p>{{ config('app.name') }} is a product of {{ config('site.parent.name') }}. This page explains what we collect and why.</p>

            <h2>What we collect</h2>
            <ul>
                <li><b>When you contact us:</b> your name, email, phone (if you give it) and your message. We use it to reply to you.</li>
                <li><b>When you sign up:</b> your name, business name, email and a password (stored in a scrambled form we cannot read).</li>
                <li><b>When a customer turns on app messages:</b> a technical address from their phone's push service so we can deliver messages. We do not collect their name, email or phone number.</li>
            </ul>

            <h2>What we do not do</h2>
            <p>We do not sell your information. We do not share it with anyone except the services we need to run {{ config('app.name') }} (for example hosting and email).</p>

            <h2>Message choices</h2>
            <p>App messages are only sent to people who chose to turn them on. They can turn them off at any time in the app, or in their phone or browser settings.</p>

            <h2>Cookies</h2>
            <p>We use cookies that are needed for signing in and keeping the site secure. We do not use advertising cookies on this website.</p>

            <h2>Your choices</h2>
            <p>You can ask us to see, correct or delete your information at any time. Use the <a href="{{ route('contact') }}">contact page</a>.</p>
        </div>
    </section>
</x-layouts.marketing>
