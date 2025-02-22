<?php

declare(strict_types=1);

namespace App\Handler;

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
        foreach ($_SESSION['man'] as $man) {
            foreach ($_SESSION['woman'] as $woman) {
                if (
                    !isset($woman['name'])
                    || !isset($woman['age'])
                    || !isset($man['name'])
                    || !isset($man['age'])
                ) {
                    continue;
                }

                if (
                    abs(strlen($woman['name']) - strlen($man['name'])) < 10
                    && abs($woman['age'] - $man['age']) < 10
                ) {
                    $potentialPair[] = [
                        'man' => $man,
                        'woman' => $woman,
                    ];
                }
            }
        }

        if (empty($potentionPair)) {
            return new EmptyResponse(StatusCodeInterface::STATUS_NO_CONTENT);
        }

        return new JsonResponse($potentialPair, StatusCodeInterface::STATUS_OK);
    }
}
