<x-layouts.marketing title="Contact" description="Tell us about your business. We build simple websites and your own app for small businesses, and we reply by email.">

    <section class="rw-banner">
        <div class="container">
            <h1>Contact Us</h1>
            <p>Tell us about your business. We will reply by email.</p>
        </div>
    </section>

    <section class="rw-section">
        <div class="container" style="max-width: 760px">
            @if (session('status'))<div class="rw-alert">{{ session('status') }}</div>@endif

            <form method="POST" action="{{ route('contact.send') }}" class="rw-form" novalidate>
                @csrf
                <div class="rw-hp" aria-hidden="true"><label>Leave this empty <input type="text" name="company_site" tabindex="-1" autocomplete="off"></label></div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name">Your name</label>
                        <input id="name" name="name" class="form-control" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
                        @error('name')<div class="err">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="190" autocomplete="email">
                        @error('email')<div class="err">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="phone">Phone (optional)</label>
                        <input id="phone" name="phone" class="form-control" value="{{ old('phone') }}" maxlength="40" autocomplete="tel">
                        @error('phone')<div class="err">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="service">I am interested in</label>
                        <select id="service" name="service" class="form-select">
                            @foreach (\App\Models\ContactMessage::SERVICES as $value => $label)
                                <option value="{{ $value }}" @selected(old('service', request('service', 'website')) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="message">Your message</label>
                        <textarea id="message" name="message" rows="6" class="form-control" required maxlength="3000" placeholder="What kind of business do you have? What would you like help with?">{{ old('message') }}</textarea>
                        @error('message')<div class="err">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12"><button class="btn_one" type="submit">Send message</button></div>
                </div>
            </form>
        </div>
    </section>
</x-layouts.marketing>
