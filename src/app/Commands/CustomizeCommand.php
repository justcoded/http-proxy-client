<?php

declare(strict_types=1);

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CustomizeCommand extends Command
{
    protected $signature = 'customize {config?} {value?}';

    public function handle(): int
    {
        $dotenvPath = base_path('.env');

        //TODO: find a way to put content in the host FS, not in the phar://
        $dotenvPath = str_replace('phar://', '', $dotenvPath);
        $dotenvPath = str_replace('/whp', '', $dotenvPath);

        if (file_exists($dotenvPath) && !$this->confirm("The .env file already exists. Do you want to overwrite it?")) {
            return static::SUCCESS;
        }

        if ($config = $this->argument('config')) {
            if (! in_array($config, array_keys($this->argumentConfigMap()))) {
                $this->error("The config \"{$config}\" is not a valid application option.");

                return static::FAILURE;
            }

            $config = Arr::get($this->argumentConfigMap(), $config);
            $value = $this->argument('value');

            if (is_null($value)) {
                $value = $this->ask("Enter the value for {$config}:");
            }

            if (in_array($value, ['true', 'false'])) {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            if (is_numeric($value)) {
                $value = (int) $value;
            }

            config([$config => $value]);
        }

        file_put_contents($dotenvPath, $this->dotenvContent());
        $this->info("The .env file has been successfully written in {$dotenvPath}");

        return static::SUCCESS;
    }

    protected function argumentConfigMap(): array
    {
        return [
            'timezone' => 'app.timezone',
            'socket.timeout' => 'whp.socket.timeout',
            'socket.self_signed_ssl' => 'whp.socket.self_signed_ssl',
            'socket.protocol_version' => 'whp.socket.protocol_version',
            'socket.client_name' => 'whp.socket.client_name',
            'socket.version' => 'whp.socket.version',
            'socket.flash' => 'whp.socket.flash',
        ];
    }

    protected function dotenvContent(): string
    {
        $dotEnv = 'APP_TIMEZONE=' . config('app.timezone') . PHP_EOL;
        $dotEnv .= PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_TIMEOUT=' . config('whp.socket.timeout') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_SELF_SIGNED_SSL=' . (config('whp.socket.self_signed_ssl') ? 'true' : 'false') . PHP_EOL;
        $dotEnv .= PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_PROTOCOL_VERSION=' . config('whp.socket.protocol_version') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_CLIENT_NAME=' . config('whp.socket.client_name') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_VERSION=' . config('whp.socket.version') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_FLASH=' . (config('whp.socket.flash') ? 'true' : 'false') . PHP_EOL;

        return $dotEnv;
    }
}
