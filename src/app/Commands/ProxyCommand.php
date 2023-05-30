<?php

declare(strict_types=1);

namespace App\Commands;

use Illuminate\Console\Command;

class ProxyCommand extends Command
{
    protected $signature = 'proxy {--channel-uuid} {--forward-url=}';

    public function handle(): int
    {
        $channelUuid = $this->option('channel-uuid');
        $forwardUrl = $this->option('forward-url');


        return static::SUCCESS;
    }
}
