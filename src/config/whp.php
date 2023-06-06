<?php

return [
    'secure' => env('WHP_SECURE', true),
    'host' => env('WHP_HOST', 'request-proxy.dev-net.co'),
    'port' => env('WHP_PORT'),
    'socket' => [
        'app_key' => env('WHP_APP_KEY', 'whp_CyJUXvwTEVGxvDKg'),
        'timeout' => env('WHP_SOCKET_TIMEOUT', 20),
        'channel_basename' => env('WHP_SOCKET_CHANNEL_BASENAME', 'App.Webhooks'),
        'self_signed_ssl' => env('WHP_SOCKET_SELF_SIGNED_SSL', false),

        'protocol' => env('WHP_SOCKET_PROTOCOL', 7),
        'client' => env('WHP_SOCKET_CLIENT', 'js'),
        'version' => env('WHP_SOCKET_VERSION', '4.4.0'),
        'flash' => env('WHP_SOCKET_FLASH', 'false'),
    ],
];
