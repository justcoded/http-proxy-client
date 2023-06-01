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
    protected Payload $payload;

    public function __construct(object $request)
    {
        $this->payload = new Payload($request->method, $request->body, (array)$request->headers);
    }

    public static function make(object $request): static
    {
        return new static($request);
    }

    public function forward(string $url): ResponseInterface
    {
        $client = new Client();

        try {
            return $client->send($this->payload->toRequest($url));
        } catch (RequestException|ProviderException|BadResponseException $e) {
            return $e->getResponse();
        } catch (GuzzleException $e) {
            return new Response(status: 500, body: $e->getMessage());
        }
    }
}
