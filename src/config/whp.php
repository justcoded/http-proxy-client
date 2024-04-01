<?php

return [
    'secure' => env('WHP_SECURE', true),
    'host' => env('WHP_HOST', 'request-proxy.dev-net.co'),
    'port' => env('WHP_PORT'),
    'socket' => [
        'app_key' => env('WHP_APP_KEY', 'whp_CyJUXvwTEVGxvDKg'),
        'timeout' => env('WHP_SOCKET_TIMEOUT', 20),
        'channel_basename' => env('WHP_SOCKET_CHANNEL_BASENAME', 'App.Webhooks'),
        'verify_ssl' => env('WHP_SOCKET_VERIFY_SSL', true),

        'protocol_version' => env('WHP_SOCKET_PROTOCOL_VERSION', 7),
        'client_name' => env('WHP_SOCKET_CLIENT_NAME', 'js'),
        'version' => env('WHP_SOCKET_VERSION', '4.4.0'),
        'flash' => env('WHP_SOCKET_FLASH', 'false'),
    ],
];
