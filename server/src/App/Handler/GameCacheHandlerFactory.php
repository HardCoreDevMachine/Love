<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;

class GameCacheHandlerFactory
{
    public function __invoke(ContainerInterface $container) : GameCacheHandler
    {
        return new GameCacheHandler();
    }
}
