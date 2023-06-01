<?php

return [
    'socket' => [
        'socket_id' => env('WHP_SOCKET_ID', 'whp'),
        'secure' => env('WHP_SOCKET_SECURE', false),
        'host' => env('WHP_SOCKET_HOST', 'httpproxy.test'),
        'port' => env('WHP_SOCKET_PORT', 6001),
        'channel_basename' => env('WHP_SOCKET_CHANNEL_BASENAME', 'App.Webhooks'),
    ],
];
