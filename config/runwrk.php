<?php

return [
    'reserved_paths' => [
        'admin', 'login', 'logout', 'api', 'dashboard', 'pricing', 'assets',
        'signup', 'register', 'password', 'build', 'storage', 'up', 'privacy',
        'terms', 'contact', 'demo', 'stripe', 'sw.js', 'manifest',
    ],

    'allow_local_origins' => (bool) env('RUNWRK_ALLOW_LOCAL_ORIGINS', env('APP_ENV') === 'local'),

    'vapid' => [
        'public' => env('VAPID_PUBLIC_KEY'),
        'private' => env('VAPID_PRIVATE_KEY'),
        'subject' => env('VAPID_SUBJECT'),
    ],
];
