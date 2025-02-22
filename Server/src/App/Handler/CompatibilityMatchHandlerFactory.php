<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;

class CompatibilityMatchHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CompatibilityMatchHandler
    {
        return new CompatibilityMatchHandler();
    }
}
