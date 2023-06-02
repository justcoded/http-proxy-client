<?php

declare(strict_types=1);

namespace App\View;

use function Termwind\render;

class View
{
    public static function render(string $path, array $data = []): void
    {
        render(static::renderHtml($path, $data));
    }

    public static function renderHtml(string $path, array $data = []): string
    {
        $path = base_path('resources/views/') . str_replace('.', '/', $path) . '.php';

        extract($data);

        ob_get_clean();
        ob_start();

        require $path;

        $html = ob_get_contents();
        ob_get_clean();

        return $html;
    }
}
