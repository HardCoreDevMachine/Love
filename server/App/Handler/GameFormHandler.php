<?php

declare(strict_types=1);

namespace App\Handler;

use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;
use App\Service\CoupleService;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Dto\PersonalDataDto;
use PHPUnit\Framework\TestStatus\Success;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GameFormHandler implements RequestHandlerInterface
{


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $formHolderRules = [
            'wifeName' => 'string',
            'wifeAge' => 'integer',
            'husbandName' => 'string',
            'husbandAge' => 'integer',
        ];
        $validator = new ValidatorHelper($formHolderRules);
        $coupleService = new CoupleService();

        $data = json_decode($request->getBody()->getContents(), true);

        try {
            $validator->arrayValidate($data);

        } catch (ValidationException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        $wife = new PersonalDataDto($data['wifeName'], $data['wifeAge']);
        $husband = new PersonalDataDto($data['husbandName'], $data['husbandAge']);
        $success = $coupleService->checkPairCompatibility($wife, $husband);
        if (!$success) {
            session_start();
            //I scared of object serialization so just data in array
            $_SESSION['free_wife'][] = [
                'name' => $wife->name,
                'age' => $wife->age,
            ];

            $_SESSION['free_husbands'][] = [
                'name' => $husband->name,
                'age' => $husband->age,
            ];

            session_write_close();
        }

        return new JsonResponse([
            'success' => $success,
            'data' => $data
        ]);
    }
}
