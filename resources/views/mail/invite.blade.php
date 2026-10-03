<x-mail::message>
# Welcome to {{ config('app.name') }}

Hi {{ $name }},

You've been given access to **{{ $businessName }}**. Choose your password to sign in:

<x-mail::button :url="$url">
Set my password
</x-mail::button>

This link works for 3 days. If you weren't expecting this, you can ignore it.
</x-mail::message>
