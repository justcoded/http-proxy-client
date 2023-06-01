<?php

declare(strict_types=1);

namespace App\Sockets;

class Client
{
    public function __construct(
        protected WebSocket $connection,
        protected string $auth = '',
    ) {}

    public static function make(WebSocket $connection, string $auth = ''): static
    {
        return new static($connection, $auth);
    }

    public function subscribe(string $channel): bool
    {
        return $this->connection->send(json_encode([
            'event' => 'pusher:subscribe',
            'data' => [
                'auth' => $this->auth,
                'channel' => $channel,
            ],
        ]));
    }

    public function unsubscribe(string $channel): bool
    {
        return $this->connection->send(json_encode([
            'event' => 'pusher:unsubscribe',
            'data' => [
                'auth' => $this->auth,
                'channel' => $channel,
            ],
        ]));
    }

    public function ping(string $channel): bool
    {
        return $this->connection->send(json_encode([
            'event' => 'pusher:ping',
            'data' => [
                'auth' => $this->auth,
                'channel' => $channel,
            ],
        ]));
    }
}
