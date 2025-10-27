<?php

return [

    'default' => env('BROADCAST_DRIVER', 'log'),

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',

            // Use Reverb creds first; fall back to PUSHER_* if present.
            'key'    => env('REVERB_APP_KEY', env('PUSHER_APP_KEY')),
            'secret' => env('REVERB_APP_SECRET', env('PUSHER_APP_SECRET')),
            'app_id' => env('REVERB_APP_ID', env('PUSHER_APP_ID')),

            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),

                // Point server at Reverb host/port by default
                'host'   => env('REVERB_HOST', env('VITE_PUSHER_HOST', '127.0.0.1')),
                'port'   => (int) env('REVERB_PORT', (int) env('VITE_PUSHER_PORT', 8080)),
                'scheme' => env('REVERB_SCHEME', env('VITE_PUSHER_SCHEME', 'http')),

                'useTLS' => env('REVERB_SCHEME', env('VITE_PUSHER_SCHEME', 'http')) === 'https',
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
