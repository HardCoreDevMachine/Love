<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;

class CorsMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : CorsMiddleware
    {
        return new CorsMiddleware();
    }
}
