<x-mail::message>
# Confirm your email

Hi {{ $name }},

Enter this code to finish creating your {{ config('app.name') }} account:

<x-mail::panel>
# {{ $code }}
</x-mail::panel>

The code is good for 15 minutes. If you didn't start a signup, you can ignore this email.
</x-mail::message>
