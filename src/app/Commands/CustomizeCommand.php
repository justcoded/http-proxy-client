<?php

declare(strict_types=1);

namespace App\Commands;

use Illuminate\Console\Command;

class CustomizeCommand extends Command
{
    protected $signature = 'customize {config?} {value?}';

    public function handle(): int
    {
        $dotenvPath = base_path('.env');
        if (file_exists($dotenvPath) && ! $this->confirm("The .env file already exists. Do you want to overwrite it?")) {
            return static::SUCCESS;
        }

        if ($config = $this->argument('config')) {
            if (! config($config)) {
                $this->error("The config \"{$config}\" is not a valid application option.");

                return static::FAILURE;
            }

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

        $dotenvPath = str_replace('phar://', '', $dotenvPath);
        $dotenvPath = str_replace('/whp', '', $dotenvPath);
        file_put_contents($dotenvPath, $this->dotenvContent());
        $this->info('The .env file has been successfully written.');

        return static::SUCCESS;
    }

    protected function dotenvContent(): string
    {
        $dotEnv = 'APP_NAME=\'' . config('app.name') . '\'' . PHP_EOL;
        $dotEnv .= 'APP_ENV=' . config('app.env') . PHP_EOL;
        $dotEnv .= 'APP_TIMEZONE=' . config('app.timezone') . PHP_EOL;
        $dotEnv .= 'APP_MAX_OUTPUT_LINE_CHARS=' . config('app.max_output_line_chars') . PHP_EOL;
        $dotEnv .= PHP_EOL;
        $dotEnv .= 'WHP_SECURE=' . config('whp.secure') . PHP_EOL;
        $dotEnv .= 'WHP_HOST=' . config('whp.host') . PHP_EOL;
        $dotEnv .= 'WHP_PORT=' . config('whp.port') . PHP_EOL;
        $dotEnv .= 'WHP_APP_KEY=' . config('whp.app_key') . PHP_EOL;
        $dotEnv .= PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_TIMEOUT=' . config('whp.socket.timeout') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_CHANNEL_BASENAME=' . config('whp.socket.channel_basename') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_SELF_SIGNED_SSL=' . config('whp.socket.self_signed_ssl') . PHP_EOL;
        $dotEnv .= PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_PROTOCOL=' . config('whp.socket.protocol') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_CLIENT=' . config('whp.socket.client') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_VERSION=' . config('whp.socket.version') . PHP_EOL;
        $dotEnv .= 'WHP_SOCKET_FLASH=' . config('whp.socket.flash') . PHP_EOL;

        return $dotEnv;
    }
}
