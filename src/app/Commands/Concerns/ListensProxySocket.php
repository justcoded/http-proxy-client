<?php

declare(strict_types=1);

namespace App\Commands\Concerns;

use Throwable;

trait ListensProxySocket
{
    protected function whpSocketUrl(): string
    {
        $scheme = config('whp.socket.secure') ? 'wss' : 'ws';
        $host = config('whp.socket.host');
        $port = config('whp.socket.port');
        $socketId = config('whp.socket.socket_id');

        return "{$scheme}://{$host}:{$port}/app/{$socketId}?protocol=7&client=js&version=4.4.0&flash=false";
    }

    protected function onError(): callable
    {
        return function (Throwable $error) {
            $this->error("Connection error: {$error->getMessage()}");
        };
    }

    protected function onClose(): callable
    {
        return function ($code = null, $reason = null) {
            $errMsg = 'Connection closed';

            if ($code) {
                $errMsg .= " ({$code})";
            }

            if ($reason) {
                $errMsg .= " - {$reason}";
            }

            $this->line($errMsg);
        };
    }
}
