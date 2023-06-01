<?php

declare(strict_types=1);

namespace App\Proxy;

use GuzzleHttp\Psr7\Request;
use stdClass;

class RequestData
{
    public readonly array $headers;

    public function __construct(
        public readonly string $method,
        protected array|object|string $body,
        array|stdClass $headers,
    ) {
        $this->headers = static::normalizeHeaders($headers);
    }

    public static function fromRaw(array|stdClass $request): static
    {
        $request = (array) $request;

        return new static(
            $request['method'],
            $request['body'],
            $request['headers'],
        );
    }

    protected static function normalizeHeaders(array|stdClass $headers): array
    {
        $headers = (array) $headers;

        return array_map(fn($header) => is_array($header) ? $header[0] : $header, $headers);
    }

    public function body(): string
    {
        if (is_string($this->body)) {
            return $this->body;
        }

        if (is_array($this->body)) {
            return json_encode($this->body);
        }

        return json_encode((array) $this->body);
    }

    public function contentType(): string
    {
        return $this->headers['content-type'] ?? 'application/json';
    }

    public function toRequest(string $url): Request
    {
        return new Request(
            $this->method,
            $url,
            $this->headers,
            $this->body(),
        );
    }
}
