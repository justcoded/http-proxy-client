<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Concerns\ForwardsProxyWebhooks;
use App\Commands\Concerns\ListensProxySocket;
use App\Sockets\Concerns\InteractsWithSockets;
use App\Sockets\PusherApi;
use App\Sockets\WebSocket;
use App\View\View;
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
        $channelUuid = $this->option('channel-uuid') ?? $this->ask('Enter the channel UUID:');
        $forwardUrl = $this->option('forward-url') ?? $this->ask('Enter the URL to forward to:');

        View::render('command.proxy.start', compact('channelUuid', 'forwardUrl'));

        $this
            ->connect($this->whpSocketUrl())
            ->then(function (WebSocket $connection) use ($channelUuid, $forwardUrl) {
                $pusher = new PusherApi($connection);
                $channel = rtrim(config('whp.socket.channel_basename'), '.') . '.' . $channelUuid;

                $pusher->subscribe($channel);

                $connection->on('error', $this->onError());
                $connection->on('close', $this->onClose());

                $connection->on('message', $this->onMessage($forwardUrl));

                $this->loop->addPeriodicTimer(
                    max(config('whp.socket.timeout') - 1, 1),
                    static fn() => $pusher->ping($channel),
                );
            }, function (Throwable $e) {
                $this->error('Could not connect: ' . $e->getMessage());
            });

        $this->loop->run();
    }
}
