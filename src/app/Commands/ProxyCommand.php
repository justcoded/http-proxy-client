<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Concerns\ForwardsProxyWebhooks;
use App\Commands\Concerns\ListensProxySocket;
use App\Sockets\Concerns\InteractsWithSockets;
use App\Sockets\PusherApi;
use App\Sockets\WebSocket;
use App\Util\WebhookProxy;
use App\View\View;
use Illuminate\Console\Command;
use InvalidArgumentException;
use React\EventLoop\LoopInterface;
use RuntimeException;
use Throwable;
use function Termwind\render;

class ProxyCommand extends Command
{
    use InteractsWithSockets, ListensProxySocket, ForwardsProxyWebhooks;

    protected $signature = 'proxy {--channel=} {--forward-url=} {--secure=true}';

    public function __construct(
        protected LoopInterface $loop,
        protected WebhookProxy $webhookProxy,
    ) {
        parent::__construct();
    }

    protected function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    public function handle(): void
    {
        $channelIdentifier = $this->option('channel') ?? $this->ask('Enter the channel UUID or webhook URL:');

        try {
            $channelUuid = $this->webhookProxy->parseChannelUuid($channelIdentifier);
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return;
        }

        $forwardUrl = $this->option('forward-url') ?? $this->ask('Enter the URL to forward to:');
        $webhookUrl = $this->webhookProxy->webhookUrl($channelUuid);
        $secureSocket = $this->option('secure') === 'false' ? false : config('whp.socket.secure');

        if ($forwardUrl === $this->webhookProxy->webhookUrl($channelUuid)) {
            $this->error('The forward URL cannot be the same as the webhook URL.');

            return;
        }

        View::render('command.proxy.start', compact('channelUuid', 'forwardUrl', 'webhookUrl'));

        $this
            ->connect($this->webhookProxy->websocketUrl($secureSocket))
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
