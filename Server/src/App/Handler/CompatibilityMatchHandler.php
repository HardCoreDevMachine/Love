<?php

declare(strict_types=1);

namespace App\Handler;

use App\Enum\GenderEnum;
use App\Helper\compabilityHelper;
use App\Dto\Person;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CompatibilityMatchHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        session_start();
        $potentialPair = [];
        foreach ($_SESSION[GenderEnum::MAN->value] as $man) {
            foreach ($_SESSION[GenderEnum::WOMAN->value] as $woman) {
                if (
                    !isset($woman['name'])
                    || !isset($woman['age'])
                    || !isset($man['name'])
                    || !isset($man['age'])
                ) {
                    continue;
                }

                $woman = new Person($woman['name'], (int)$woman['age']);
                $man = new Person($man['name'], (int)$man['age']);


                if (
                    compabilityHelper::compabilityCheck($woman, $man)
                ) {
                    $potentialPair[] = [
                        'woman' => $woman->toArray(),
                        'man' => $man->toArray(),
                    ];
                }
            }
        }

        if (empty($potentionPair)) {
            return new EmptyResponse(StatusCodeInterface::STATUS_NO_CONTENT);
        }

        return new JsonResponse(['potential_pairs' => $potentialPair], StatusCodeInterface::STATUS_OK);
    }
}
