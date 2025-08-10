<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;

class GameFormHandlerFactory
{
    public function __invoke(ContainerInterface $container): GameFormHandler
    {
        return new GameFormHandler();
    }
}
