<?php

declare(strict_types=1);

namespace App\Commands\Concerns;

use Throwable;

trait ListensProxySocket
{
    protected function onError(): callable
    {
        return function (Throwable $error) {
            $this->error("Connection error: {$error->getMessage()}");
        };
    }

    protected function onClose(): callable
    {
        return function ($code = null, $reason = null) {
            $errMsg = 'Connection closed';

            if ($code) {
                $errMsg .= " ({$code})";
            }

            if ($reason) {
                $errMsg .= " - {$reason}";
            }

            $this->line($errMsg);
        };
    }
}
