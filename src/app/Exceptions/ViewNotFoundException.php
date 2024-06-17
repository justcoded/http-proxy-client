<?php

namespace App\Exceptions;

use InvalidArgumentException;
use Throwable;

class ViewNotFoundException extends InvalidArgumentException
{
    public function __construct(string $viewPath = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("View [{$viewPath}] does not exist.", $code, $previous);
    }
}
