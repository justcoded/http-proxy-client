<?php

declare(strict_types=1);

namespace App\Commands\Concerns;

use App\Proxy\RequestData;
use App\Proxy\RequestForwarder;
use Carbon\Carbon;
use Ratchet\RFC6455\Messaging\Message;
use function Termwind\render;

trait ForwardsProxyWebhooks
{
    protected function onMessage(string $forwardUrl): callable
    {
        return function (Message $message) use ($forwardUrl) {
            $msg = json_decode($message->getPayload());
            if (! $msg->event) {
                return;
            }

            $data = null;
            if (isset($msg->data)) {
                $data = is_string($msg->data) ? json_decode($msg->data) : $msg->data;
            }

            if (! $data || ! isset($data->request)) {
                return;
            }

            render('<hr>'); // main delimiter

            $requestData = RequestData::fromRaw($data->request);

            render(<<<HTML
                <dl>
                  <dt>Request origin: </dt>
                  <dd>{$requestData->method} {$requestData->headers['host']}</dd>
                  <dt>Payload: </dt>
                  <dd>{$requestData->body()}</dd>
                </dl>
            HTML);

            $start = microtime(true);
            $response = RequestForwarder::make($data->request)->forward($forwardUrl);
            $forwardedInSeconds = round(microtime(true) - $start, 3) . 's';

            $body = $response->getBody()->getContents() ?: '(empty)';

            render(<<<HTML
                <dl>
                    <dt>Response: </dt>
                    <dd>{$response->getStatusCode()} {$response->getReasonPhrase()}</dd>
                    <dt>Response time: </dt>
                    <dd>{$forwardedInSeconds}</dd>
                    <dt>Response payload: </dt>
                    <dd>{$body}</dd>
                </dl>
            HTML);
        };
    }
}
