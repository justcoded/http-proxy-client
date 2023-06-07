<?php

declare(strict_types=1);

namespace App\Sockets\Concerns;

use App\Sockets\Connector;
use React\EventLoop\LoopInterface;

trait InteractsWithSockets
{
    abstract protected function getLoop(): LoopInterface;

    protected function connect($url, array $subProtocols = [], $headers = [])
    {
        $connector = new Connector($this->getLoop());

        return $connector($url, $subProtocols, $headers);
    }
}
