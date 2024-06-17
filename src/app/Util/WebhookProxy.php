<?php

declare(strict_types=1);

namespace App\Util;

use Illuminate\Support\Str;
use InvalidArgumentException;

class WebhookProxy
{
    protected string $host;
    protected string $scheme = '';
    protected bool $secure;
    protected int|string|null $port = null;

    public function __construct()
    {
        $this->host = config('whp.host');
        $this->port = config('whp.port');
        $this->secure = config('whp.secure');
        $this->scheme = $this->secure ? 'https' : 'http';
    }

    public function baseUrl(): string
    {
        $port = $this->port ? ":{$this->port}" : '';

        return "{$this->scheme}://{$this->host}{$port}";
    }

    public function parseChannelUuid(string $channelIdentifier): string
    {
        if (! Str::startsWith($channelIdentifier, 'http')) {
            $this->ensureValidUuid($channelIdentifier);

            return $channelIdentifier;
        }

        $this->scheme = parse_url($channelIdentifier, PHP_URL_SCHEME);
        $this->host = parse_url($channelIdentifier, PHP_URL_HOST);
        $this->port = parse_url($channelIdentifier, PHP_URL_PORT);

        $channelUuid = Str::afterLast($channelIdentifier, '/');
        $this->ensureValidUuid($channelUuid);

        return $channelUuid;
    }

    protected function ensureValidUuid(string $channelUuid): void
    {
        $uuidRegex = '/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/';
        if (! preg_match($uuidRegex, $channelUuid)) {
            throw new InvalidArgumentException('Invalid channel UUID provided.');
        }
    }

    public function channelUrl(int $channelId): string
    {
        return $this->baseUrl() . '/channels/' . $channelId;
    }

    public function requestUrl(int $channelId, int $requestId): string
    {
        return $this->channelUrl($channelId) . '/' . $requestId;
    }

    public function webhookUrl(string $channelUuid): string
    {
        return $this->baseUrl() . "/ch/{$channelUuid}";
    }

    public function websocketUrl(): string
    {
        $scheme = $this->secure ? 'wss' : 'ws';
        $port = $this->port ? ":{$this->port}" : '';
        $appKey = config('whp.socket.app_key');

        $protocol = config('whp.socket.protocol');
        $client = config('whp.socket.client');
        $version = config('whp.socket.version');
        $flash = config('whp.socket.flash');

        return "{$scheme}://{$this->host}{$port}/app/{$appKey}?protocol={$protocol}&client={$client}&version={$version}&flash={$flash}";
    }
}
