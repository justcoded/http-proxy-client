<?php

return [
    'secure' => env('WHP_SECURE', true),
    'host' => env('WHP_HOST', 'request-proxy.dev-net.co'),
    'port' => env('WHP_PORT'),
    'socket' => [
        'app_key' => env('WHP_APP_KEY', 'whp_CyJUXvwTEVGxvDKg'),
        'secure' => env('WHP_SOCKET_SECURE', true),
        'port' => env('WHP_SOCKET_PORT', 6001),
        'timeout' => env('WHP_SOCKET_TIMEOUT', 20),
        'channel_basename' => env('WHP_SOCKET_CHANNEL_BASENAME', 'App.Webhooks'),
    ],
];
