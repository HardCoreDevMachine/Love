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
        $this->validator = Validation::createValidator();
        $this->constraints = new Assert\Collection([
            'name'      => [
                new Assert\NotBlank(),
            ],
            'age' => [
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
                new Assert\GreaterThanOrEqual(18),
            ],
        ]);
    }

    public function validate() {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = json_decode((string)$request->getBody(), true);
        foreach ($body as $gender) {
            $violations = $this->validator->validate($gender, $this->constraints);

            // Проверяем наличие ошибок
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
                return new JsonResponse([
                    'message' => 'send an encorrect data',
                    'error' => $errors,
                ], StatusCodeInterface::STATUS_BAD_REQUEST);
            }
        }

        return $handler->handle($request);
    }
}
