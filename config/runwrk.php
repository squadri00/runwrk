<?php

return [
    'reserved_paths' => [
        'admin', 'login', 'logout', 'api', 'dashboard', 'pricing', 'assets',
        'signup', 'register', 'password', 'build', 'storage', 'up', 'privacy',
        'terms', 'contact', 'demo', 'stripe', 'sw.js', 'manifest', 'web-design', 'your-app', 'sitemap.xml', 'robots.txt',
    ],

    'allow_local_origins' => (bool) env('RUNWRK_ALLOW_LOCAL_ORIGINS', env('APP_ENV') === 'local'),

    'vapid' => [
        'public' => env('VAPID_PUBLIC_KEY'),
        'private' => env('VAPID_PRIVATE_KEY'),
        'subject' => env('VAPID_SUBJECT', 'mailto:hello@runwrk.com'),
    ],

    'push' => [
        'batch_size' => (int) env('PUSH_BATCH_SIZE', 100),
        'ttl' => (int) env('PUSH_TTL', 86400),
        // "high" wakes an idle phone right away; "normal" can be held back for a long time while the phone sleeps.
        'urgency' => env('PUSH_URGENCY', 'high'),
        'drop_after_failures' => 5,
        // Subscription endpoints must belong to a real push service (we POST to them).
        'allowed_hosts' => ['googleapis.com', 'push.services.mozilla.com', 'push.apple.com', 'notify.windows.com'],
    ],
];
