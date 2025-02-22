<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    public function __construct(
        private string $containerName,
        private RouterInterface $router,
        private ?TemplateRendererInterface $template = null
    ) {}

    //TODO: Тут Мы сделаем возращение фала билда или перевод на запущеный веб сервер если у нас отладка
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        //TODO: Вынынести путь к фаул в конфиг провайдер
        $html = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/main.html');
        return new HtmlResponse($html);
    }
}
