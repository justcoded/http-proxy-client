<?php

namespace App\Providers;

use App\Util\WebhookProxy;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoopInterface::class, fn() => Loop::get());
        $this->app->singleton(WebhookProxy::class);
    }
}
