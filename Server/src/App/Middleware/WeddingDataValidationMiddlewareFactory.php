<?php

declare(strict_types=1);

namespace App;

use Psr\Container\ContainerInterface;

class WeddingDataValidationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : WeddingDataValidationMiddleware
    {
        return new WeddingDataValidationMiddleware();
    }
}
