<?php

declare(strict_types=1);

namespace App\Handler;

use App\Enum\GenderEnum;
use App\Helper\CompabilityHelper;
use App\Dto\Person;
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
        $body = json_decode((string)$request->getBody(), true);

        $woman = new Person($body['woman']['name'], (int)$body['woman']['age']);
        $man = new Person($body['man']['name'], (int)$body['man']['age']);

        $compatibility = CompabilityHelper::compabilityCheck($woman, $man);

        //TODO: Нужно ещё проверку на недопуск повторений орагнизовать
        if (!$compatibility) {
            $_SESSION[GenderEnum::WOMAN->value][] = $woman->toArray();
            $_SESSION[GenderEnum::MAN->value][] = $man->toArray();
        }
        return new JsonResponse(['compability' => $compatibility], StatusCodeInterface::STATUS_OK);
    }
}
