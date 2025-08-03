<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

//Это только MVP - сильно не осуждать
const STATUS_BAD_REQUEST = 400;
const STATUS_NO_CONTENT = 204;

class PersonalDataDto
{
    public function __construct(
        public readonly string $name,
        public readonly int $age,
    ) {
    }
}

class ValidationException extends Exception
{
}

/**
 * Check couple compatibility
 *
 * @param PersonalDataDto $wife
 * @param PersonalDataDto $husband
 * @return bool
 */
function checkPairCompatibility(PersonalDataDto $wife, PersonalDataDto $husband): bool
{
    $acceptableDifference = 10;

    return
        abs(mb_strlen($husband->name) - mb_strlen($wife->name)) <= $acceptableDifference
        && abs($husband->age - $wife->age) <= $acceptableDifference;
}

/**
 * Finds compatible pairs from given arrays of available husbands and wives.
 *
 * @param array{
 *     free_husband: array<array-key, array{name: string, age: int}>,
 *     free_wife: array<array-key, array{name: string, age: int}>
 * } $people Associative array containing:
 *             - free_husband: List of available husbands
 *             - free_wife: List of available wives
 *
 * @return array<array{
 *     wife: PersonalDataDto,
 *     husband: PersonalDataDto
 * }> Returns an array of compatible pairs where each pair contains DTO objects
 */
function findPotentialPair(array $people): array
{
    if (empty($people)) {
        return [];
    }

    $pairs = [];

    foreach ($people['free_wife'] as $wifeData) {
        foreach ($people['free_husband'] as $husbandData) {
            $wife = new PersonalDataDto($wifeData['name'], $wifeData['age']);
            $husband = new PersonalDataDto($husbandData['name'], $husbandData['age']);

            if (checkPairCompatibility($wife, $husband)) {
                $pairs[] = [
                    'wife' => $wife,
                    'husband' => $husband
                ];
            }
        }
    }

    return $pairs;
}

/**
 * Validate form data for game rule
 *
 * @param array $body
 * @param array $rules
 * @return void
 *
 * @throws Exception
 */
function dataValidation(array $body, array $rules): void
{
    array_walk($rules, static function ($type, $name) use ($body): void {
        if (!isset($body[$name]) || empty($body[$name]) && gettype($body[$name]) === $type) {
            throw new ValidationException('Недопустимо передавать незаполненное поле - ' . $name . ' типа -' . gettype($body[$name]));
        }
    });
}

$body = json_decode(file_get_contents('php://input'), true);
$response = [];

if (empty($body)) {
    http_response_code(STATUS_NO_CONTENT);
    return;
}

if (isset($_POST['cache-calc'])) {
    session_start();
    $formHolderNames = [
        'free_wife' => 'array',
        'free_husbands' => 'array',
    ];
    dataValidation($body, $formHolderNames);
    $pairs = findPotentialPair($_SESSION);
    session_write_close();
}

try {
    $formHolderNames = [
        'wifeName' => 'string',
        'wifeAge' => 'int',
        'husbandName' => 'string',
        'husbandAge' => 'int',
    ];
    dataValidation($body, $formHolderNames);
    $wife = new PersonalDataDto($body['wifeName'], $body['wifeAge']);
    $husband = new PersonalDataDto($body['husbandName'], $body['husbandAge']);

    $result = checkPairCompatibility($wife, $husband);
    $response['success'] = $result;
} catch (Throwable $e) {
    http_response_code(STATUS_BAD_REQUEST);
    $response['error'] = $e->getMessage();
    die;
}

if (!$result) {
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

echo json_encode($response);
