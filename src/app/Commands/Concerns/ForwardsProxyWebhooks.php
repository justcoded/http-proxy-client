<?php

declare(strict_types=1);

namespace App\Commands\Concerns;

use App\Proxy\RequestData;
use App\Proxy\RequestForwarder;
use App\Util\WebhookProxy;
use App\View\View;
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

            render('<hr>');

            $tz = config('app.timezone');
            $timestamp = Carbon::now()->setTimezone($tz)->toDateTimeString();
            render("<h2 class='font-bold italic text-center text-lime-500'>Webhook received: {$timestamp} ({$tz})</h2>");

            $requestData = RequestData::fromRaw($data->request);
            $start = microtime(true);
            $response = RequestForwarder::make($data->request)->forward($forwardUrl);
            $forwardedInSeconds = round(microtime(true) - $start, 3) . 's';

            $requestUrl = app(WebhookProxy::class)->requestUrl(
                $data->request->channel_id,
                $data->request->id,
            );

            View::render('command.proxy.webhook', [
                'timestamp' => $timestamp,
                'requestData' => $requestData,
                'response' => $response,
                'forwardedInSeconds' => $forwardedInSeconds,
                'requestUrl' => $requestUrl,
            ]);
        };
    }
}
