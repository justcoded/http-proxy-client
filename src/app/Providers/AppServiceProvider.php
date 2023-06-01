<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoopInterface::class, function () {
            return Loop::get();
        });
    }
}
