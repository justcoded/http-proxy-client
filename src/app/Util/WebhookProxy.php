<?php

declare(strict_types=1);

namespace App\Util;

use Illuminate\Support\Str;
use RuntimeException;

class WebhookProxy
{
    public function baseUrl(): string
    {
        $host = config('whp.host');
        $port = config('whp.port');
        $secure = config('whp.secure');

        $scheme = $secure ? 'https' : 'http';
        $port = $port ? ":{$port}" : '';

        return "{$scheme}://{$host}{$port}";
    }

    public function parseChannelUuid(string $channelIdentifier): string
    {
        if (! Str::startsWith('http', $channelIdentifier)) {
            $uuidRegex = '/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/';
            if (! preg_match($uuidRegex, $channelIdentifier)) {
                throw new RuntimeException('Invalid channel UUID provided.');
            }

            return $channelIdentifier;
        }

        if (! Str::contains($channelIdentifier, $this->baseUrl())) {
            throw new RuntimeException('Invalid webhook URL provided.');
        }

        return Str::afterLast($channelIdentifier, '/');
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
        $scheme = config('whp.socket.secure') ? 'wss' : 'ws';
        $host = config('whp.host');
        $port = config('whp.socket.port');
        $appId = config('whp.socket.app_id');

        return "{$scheme}://{$host}:{$port}/app/{$appId}?protocol=7&client=js&version=4.4.0&flash=false";
    }
}
