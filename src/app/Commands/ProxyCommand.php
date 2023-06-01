<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Concerns\ForwardsProxyWebhooks;
use App\Commands\Concerns\ListensProxySocket;
use App\Sockets\Client;
use App\Sockets\Concerns\InteractsWithSockets;
use App\Sockets\WebSocket;
use Illuminate\Console\Command;
use React\EventLoop\LoopInterface;
use Throwable;

class ProxyCommand extends Command
{
    use InteractsWithSockets, ListensProxySocket, ForwardsProxyWebhooks;

    protected $signature = 'proxy {--channel-uuid=} {--forward-url=}';

    public function __construct(
        protected LoopInterface $loop,
    ) {
        parent::__construct();
    }

    protected function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    public function handle(): void
    {
        if (!$channelUuid = $this->option('channel-uuid')) {
            $channelUuid = $this->ask('Enter the channel UUID:');
        }

        if (!$forwardUrl = $this->option('forward-url')) {
            $forwardUrl = $this->ask('Enter the URL to forward to:');
        }

        $this
            ->connect($this->whpSocketUrl())
            ->then(function (WebSocket $connection) use ($channelUuid, $forwardUrl) {
                $client = new Client($connection);
                $channel = rtrim(config('whp.socket.channel_basename'), '.') . '.' . $channelUuid;
                $client->subscribe($channel);

                $connection->on('error', $this->onError());
                $connection->on('close', $this->onClose());

                $connection->on('message', $this->onMessage($forwardUrl));
            }, function (Throwable $e) {
                $this->error('Could not connect: ' . $e->getMessage());
            });

        $this->loop->run();
    }
}
