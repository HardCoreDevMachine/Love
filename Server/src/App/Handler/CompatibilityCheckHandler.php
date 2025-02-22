<?php

declare(strict_types=1);

namespace App\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CompatibilityCheckHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        session_start();
        $body = $request->getParsedBody();

        $compatibility = (
            abs(strlen($body['womanName']) - strlen($body['manName'])) < 10
            && abs($body['womanAge'] - $body['manAge']) < 10
        );

        //TODO: Нужно ещё проверку на недопуск повторений орагнизовать
        if (!$compatibility) {
            $_SESSION['woman'][] = [
                'name' => $body['womanName'],
                'age' => $body['womanAge']
            ];
            $_SESSION['man'][] = [
                'name' => $body['manName'],
                'age' => $body['manAge']
            ];
        }

        return new JsonResponse(['compability' => $compatibility], StatusCodeInterface::STATUS_OK);
    }
}
