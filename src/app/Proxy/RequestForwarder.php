<?php

declare(strict_types=1);

namespace App\Proxy;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Translation\Exception\ProviderException;

class RequestForwarder
{
    protected RequestData $payload;

    protected Client $httpClient;

    public function __construct(object $request)
    {
        $contentType = $request->headers->{'content-type'}[0] ?? 'application/json';

        $body = str_contains($contentType, 'text') && isset($request->body->raw)
            ? $request->body->raw
            : $request->body;

        $this->payload = new RequestData(
            $request->method,
            $body,
            (array) $request->query,
            (array) $request->headers,
        );

        $this->httpClient = new Client([
            'verify' => false,
        ]);
    }

    public static function make(object $request): static
    {
        return new static($request);
    }

    public function forward(string $url): ResponseInterface
    {
        try {
            return $this->httpClient->send($this->payload->toRequest($url), [
                'query' => $this->payload->query,
            ]);
        } catch (RequestException|ProviderException|BadResponseException $e) {
            return $e->getResponse() ?? new Response(status: 500, body: $e->getMessage());
        } catch (GuzzleException $e) {
            return new Response(status: 500, body: $e->getMessage());
        }
    }
}
