<?php

declare(strict_types=1);

namespace App\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class WeddingDataValidationMiddleware implements MiddlewareInterface
{
    public $validator;
    private $constraints;

    public function __construct()
    {
        $this->constraints = new Assert\Collection([
            'womanName'      => [
                new Assert\NotBlank(),
            ],
            'womanAge' => [
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
                new Assert\GreaterThanOrEqual(18),
            ],
            'manName'      => [
                new Assert\NotBlank(),
            ],
            'manAge' => [
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
                new Assert\GreaterThanOrEqual(18),
            ],
        ]);
    }

    public function validate() {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //TODO: Clean from html chars and other shit
        $body = $request->getParsedBody();
        $validator = Validation::createValidator();
        if (!$validator->validate($body, $this->constraints)) {
            return new JsonResponse('Отправлены некоректные данные', StatusCodeInterface::STATUS_BAD_REQUEST);
        }
        return $handler->handle($request);
    }
}
