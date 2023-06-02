<?php

declare(strict_types=1);

namespace App\Util;

use Illuminate\Support\Str;
use InvalidArgumentException;

class WebhookProxy
{
    protected string $host;
    protected string $scheme = '';
    protected int|string|null $port = null;

    public function __construct()
    {
        $this->host = config('whp.host');
        $this->port = config('whp.port');
    }

    public function baseUrl(): string
    {
        if (!$this->scheme) {
            $secure = config('whp.secure');
            $this->scheme = $secure ? 'https' : 'http';
        }

        $port = $this->port ? ":{$this->port}" : '';

        return "{$this->scheme}://{$this->host}{$port}";
    }

    public function parseChannelUuid(string $channelIdentifier): string
    {
        if (!Str::startsWith($channelIdentifier, 'http')) {
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
        if (!preg_match($uuidRegex, $channelUuid)) {
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

    public function websocketUrl(bool $secure = true): string
    {
        $scheme = $secure ? 'wss' : 'ws';
        $port = config('whp.socket.port');
        $appId = config('whp.socket.app_id');

        return "{$scheme}://{$this->host}:{$port}/app/{$appId}?protocol=7&client=js&version=4.4.0&flash=false";
    }
}
