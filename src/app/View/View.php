<?php

declare(strict_types=1);

namespace App\View;

use App\Exceptions\ViewNotFoundException;
use InvalidArgumentException;
use function Termwind\render;

class View
{
    public static function render(string $path, array $data = []): void
    {
        render(static::renderView($path, $data));
    }

    public static function renderView(string $path, array $data = []): string
    {
        $path = static::viewPath($path);

        extract($data);

        ob_get_clean();
        ob_start();

        require $path;

        $renderedView = ob_get_contents();
        ob_get_clean();

        return $renderedView;
    }

    public static function exists(string $path): bool
    {
        try {
            static::viewPath($path);
        } catch (ViewNotFoundException) {
            return false;
        }

        return true;
    }

    protected static function viewPath(string $path): string
    {
        $path = base_path('resources/views/') . str_replace('.', '/', $path) . '.php';

        if (! file_exists($path)) {
            throw new ViewNotFoundException($path);
        }

        return $path;
    }
}
