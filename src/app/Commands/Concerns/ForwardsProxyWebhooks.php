<?php

declare(strict_types=1);

namespace App\Commands\Concerns;

use App\Proxy\Payload;
use App\Proxy\RequestForwarder;
use Carbon\Carbon;
use Ratchet\RFC6455\Messaging\Message;

trait ForwardsProxyWebhooks
{
    protected function onMessage(string $forwardUrl): callable
    {
        return function (Message $message) use ($forwardUrl) {
            $msg = json_decode($message->getPayload());
            if (!$msg->event) {
                return;
            }

            $data = isset($msg->data) ? json_decode($msg->data) : null;
            if (!isset($data->request)) {
                return;
            }

            $this->line('');
            $this->line(str_repeat('.', 30) . '(new webhook)'); // main delimiter
            $this->line('');

            $payload = Payload::fromRaw($data->request);
            $this->line('Request received: ' . Carbon::now()->setTimezone(date_default_timezone_get())->toDateTimeString());
            $this->line("Request origin: {$payload->method} {$payload->headers['host']}");
            $this->line("Payload: {$payload->body()}");

            $this->line(str_repeat('_', 15)); // request/response delimiter

            $start = microtime(true);
            $response = RequestForwarder::make($data->request)->forward($forwardUrl);
            $this->line('Forwarded in ' . round(microtime(true) - $start, 3) . 's');

            $body = $response->getBody()->getContents();
            if (empty($body)) {
                $body = '(empty)';
            }

            $this->line("Response: {$response->getStatusCode()} {$response->getReasonPhrase()}");
            $this->line("Body: {$body}");
        };
    }
}
