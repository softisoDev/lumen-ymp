<?php


return [
    'name'            => env('APP_NAME', 'Lumen'),
    'env'             => env('APP_ENV', 'production'),
    'debug'           => env('APP_DEBUG', false),
    'url'             => env('APP_URL', 'http://your-domain.com'),
    'timezone'        => 'UTC',
    'key'             => env('APP_KEY'),
    'cipher'          => 'AES-256-CBC',
    'locale'          => 'en',
    'locales'         => ['en', 'ru', 'tr'],
    'fallback_locale' => 'en',
    'throttle'        => [
        'max'        => 20,
        'minute'     => 1,
        'log_minute' => 10080
    ],
    'audio'           => [
        'limit' => 5,
        'ips'   => [],
        'keys'  => [
        ]
    ]
];
