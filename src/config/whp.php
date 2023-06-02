<?php

return [
    'secure' => env('WHP_SECURE', true),
    'host' => env('WHP_HOST', 'request-proxy.dev-net.co'),
    'port' => env('WHP_PORT'),
    'socket' => [
        'app_id' => env('WHP_APP_ID', 'whp'),
        'secure' => env('WHP_SOCKET_SECURE', true),
        'port' => env('WHP_SOCKET_PORT', 6001),
        'timeout' => env('WHP_SOCKET_TIMEOUT', 20),
        'channel_basename' => env('WHP_SOCKET_CHANNEL_BASENAME', 'App.Webhooks'),
    ],
];
