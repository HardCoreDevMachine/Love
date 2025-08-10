<?php

declare(strict_types=1);

namespace App\Handler;

use Chubbyphp\Container\MinimalContainer;
use DI\Container as PHPDIContainer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\Plates\PlatesRenderer;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\LaminasRouter;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mezzio\Twig\TwigRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class HomePageHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly string $containerName,
        private readonly RouterInterface $router,
        private readonly ?string $template = null
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [];

        switch ($this->containerName) {
            case ContainerBuilder::class:
                $data['containerName'] = 'Symfony DI Container';
                $data['containerDocs'] = 'https://symfony.com/doc/current/service_container.html';
                break;
        }

        if ($this->router instanceof FastRouteRouter) {
            $data['routerName'] = 'FastRoute';
            $data['routerDocs'] = 'https://github.com/nikic/FastRoute';
        }

        if ($this->template === null) {
            return new JsonResponse([
                'welcome' => 'Congratulations! You have installed the mezzio skeleton application.',
                'docsUrl' => 'https://docs.mezzio.dev/mezzio/',
            ] + $data);
        }

        return new HtmlResponse($this->template);
    }
}
