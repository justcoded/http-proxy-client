<?php

namespace App\Providers;

use App\Commands\HelpCommand;
use App\Util\WebhookProxy;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Command\HelpCommand as SymfonyHelpCommand;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoopInterface::class, fn() => Loop::get());
        $this->app->singleton(WebhookProxy::class);

        $this->app->bind(SymfonyHelpCommand::class, HelpCommand::class);
    }
}
