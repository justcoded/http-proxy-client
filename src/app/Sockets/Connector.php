<?php

declare(strict_types=1);

namespace App\Sockets;

use DomainException;
use Exception;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Ratchet\RFC6455\Handshake\ClientNegotiator;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface;
use React\Socket\ConnectorInterface;
use RuntimeException;
use function React\Promise\reject;

class Connector
{
    protected ClientNegotiator $negotiator;

    public function __construct(
        protected LoopInterface      $loop,
        protected ?ConnectorInterface $connector = null
    ) {
        if (! $this->connector) {
            $this->connector = new \React\Socket\Connector([
                'timeout' => 20
            ], $this->loop);
        }

        $this->negotiator = new ClientNegotiator;
    }

    public function __invoke(string $url, array $subProtocols = [], array $headers = []): PromiseInterface
    {
        try {
            $request = $this->generateRequest($url, $subProtocols, $headers);
            $uri = $request->getUri();
        } catch (Exception $e) {
            return reject($e);
        }

        $secure = str_starts_with($url, 'wss');
        $connector = $this->connector;

        $port = $uri->getPort() ?: ($secure ? 443 : 80);

        $scheme = $secure ? 'tls' : 'tcp';

        $uriString = $scheme . '://' . $uri->getHost() . ':' . $port;

        $connecting = $connector->connect($uriString);

        $futureWsConn = new Deferred(function ($_, $reject) use ($url, $connecting) {
            $reject(new RuntimeException(
                'Connection to ' . $url . ' cancelled during handshake'
            ));

            // either close active connection or cancel pending connection attempt
            $connecting->then(function (ConnectionInterface $connection) {
                $connection->close();
            });
            $connecting->cancel();
        });

        $connecting->then(function (ConnectionInterface $conn) use ($request, $subProtocols, $futureWsConn) {
            $earlyClose = function () use ($futureWsConn) {
                $futureWsConn->reject(new RuntimeException('Connection closed before handshake'));
            };

            $stream = $conn;

            $stream->on('close', $earlyClose);
            $futureWsConn->promise()->then(function () use ($stream, $earlyClose) {
                $stream->removeListener('close', $earlyClose);
            });

            $buffer = '';
            $headerParser = function ($data) use ($stream, &$headerParser, &$buffer, $futureWsConn, $request, $subProtocols) {
                $buffer .= $data;
                if (!strpos($buffer, "\r\n\r\n")) {
                    return;
                }

                $stream->removeListener('data', $headerParser);

                $response = Message::parseResponse($buffer);

                if (! $this->negotiator->validateResponse($request, $response)) {
                    $futureWsConn->reject(new DomainException(Message::toString($response)));
                    $stream->close();

                    return;
                }

                $acceptedProtocol = $response->getHeader('Sec-WebSocket-Protocol');
                if ((count($subProtocols) > 0) && 1 !== count(array_intersect($subProtocols, $acceptedProtocol))) {
                    $futureWsConn->reject(new DomainException('Server did not respond with an expected Sec-WebSocket-Protocol'));
                    $stream->close();

                    return;
                }

                $futureWsConn->resolve(new WebSocket($stream, $response, $request));

                $futureWsConn->promise()->then(function (WebSocket $conn) use ($stream) {
                    $stream->emit('data', [$conn->response->getBody(), $stream]);
                });
            };

            $stream->on('data', $headerParser);
            $stream->write(Message::toString($request));
        }, array($futureWsConn, 'reject'));

        return $futureWsConn->promise();
    }

    protected function generateRequest($url, array $subProtocols, array $headers): RequestInterface
    {
        $uri = Utils::uriFor($url);

        $scheme = $uri->getScheme();

        if (! in_array($scheme, ['ws', 'wss'])) {
            throw new InvalidArgumentException(sprintf('Cannot connect to invalid URL (%s)', $url));
        }

        $uri = $uri->withScheme('wss' === $scheme ? 'HTTPS' : 'HTTP');

        $headers += ['User-Agent' => 'Ratchet-Pawl/0.4.1'];

        $request = array_reduce(array_keys($headers), function (RequestInterface $request, $header) use ($headers) {
            return $request->withHeader($header, $headers[$header]);
        }, $this->negotiator->generateRequest($uri));

        if (! $request->getHeader('Origin')) {
            $request = $request->withHeader('Origin', str_replace('ws', 'http', $scheme) . '://' . $uri->getHost());
        }

        if (count($subProtocols) > 0) {
            $protocols = implode(',', $subProtocols);
            if ($protocols != '') {
                $request = $request->withHeader('Sec-WebSocket-Protocol', $protocols);
            }
        }

        return $request;
    }
}
