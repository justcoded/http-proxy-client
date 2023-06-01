<?php

return [
    'socket' => [
        'app_id' => env('WHP_APP_ID'),
        'secure' => env('WHP_SOCKET_SECURE', false),
        'host' => env('WHP_SOCKET_HOST'),
        'port' => env('WHP_SOCKET_PORT', 6001),
        'timeout' => env('WHP_SOCKET_TIMEOUT', 20),
        'channel_basename' => env('WHP_SOCKET_CHANNEL_BASENAME', 'App.Webhooks'),
    ],
];
