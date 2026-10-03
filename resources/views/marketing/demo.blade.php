<x-layouts.marketing title="Demo" description="See Runwrk in action. Try the demo app on your own phone.">

    <section class="rw-banner">
        <div class="container">
            <h1>Demo</h1>
            <p>See it for yourself.</p>
        </div>
    </section>

    <section class="rw-section">
        <div class="container" style="max-width: 900px">
            <div class="rw-head"><h2>Website demos</h2><p>Coming soon.</p></div>

            <div class="rw-card text-center mt-5">
                <h3>Try the app on your phone</h3>
                <p>Scan this code with your phone, add the demo barber shop app to your home screen, and turn on messages. We can then send you a message.</p>
                <div class="d-inline-block bg-white p-2 border rounded">{!! $qr !!}</div>
                <p class="mt-3 mb-0"><a class="more" href="{{ $demoUrl }}">{{ $demoUrl }}</a></p>
            </div>
        </div>
    </section>
</x-layouts.marketing>
