<?php
//Это только MVP - сильно не осуждать
const STATUS_BAD_REQUEST = 400;

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
 *
 * @throws InvalidArgumentException If input structure is invalid or required keys are missing
 */
function findPotentialPair(array $people): array
{
    if (empty($people)) {
        return [];
    }

    if (!isset($people['free_wife'], $people['free_husband'])) {
        throw new InvalidArgumentException('Отсутствуют поля с женихами или невестами');
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
 * validate form data for game rule
 *
 * @param array $body
 * @return void
 *
 * @throws Exception
 */
function gameFormValidation(array $body): void
{
    $formHolderNames = [
        'wife-name' => 'string',
        'wife-age' => 'int',
        'husband-name' => 'string',
        'husband-age' => 'int',
    ];

    array_walk($formHolderNames, static function ($type, $name) use ($body): void {
        if (!isset($body[$name]) || empty($body[$name]) && gettype($body[$name]) === $type) {
            throw new ValidationException('Недопустимо передавать незаполненное поле - ' . $name . ' типа -' . gettype($body[$name]));
        }
    });
}

if (!empty($_POST)) {
    try {
        $body = $_POST;
        var_dump($body);
        gameFormValidation($body);
        $wife = new PersonalDataDto($body['wife-name'], $body['wife-age']);
        $husband = new PersonalDataDto($body['husband-name'], $body['husband-age']);

        $result = checkPairCompatibility($wife, $husband);
    } catch (Exception $e) {
        http_response_code(STATUS_BAD_REQUEST);
        echo $e->getMessage();
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
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        :root {
            --main-color: #fe7f2d;
            --background-color: #233d4d;
        }

        * {
            color: var(--main-color);
            background-color: var(--background-color);
            font-family: Verdana, Geneva, sans-serif;
        }

        input {
            margin-top: 0.5rem;
            outline-width: 0;
            border-color: var(--main-color);
        }

        input::placeholder {
            font-weight: bold;
            opacity: 0.5;
            color: var(--main-color);
        }
    </style>
</head>

<body>
    <?php if (!isset($result) && $_SERVER['REQUEST_METHOD'] === 'GET') { ?>

        <h1>ЗАДАНИЕ</h1>
        <p>
            создать веб форму реализовать игру
            удаленная симпатия которая позволяет определить совместимость людей
            по их имени и по возрасту, если разница в возрасте менее 10 лет и разница суммы
            букв в фио влюбленных менее 10,то они подходят друг другу если условия не выполняются
            то не подходят друг другу
        </p>
        <p>
            в этой же странице реализовать многократный перебор спутников
            реализовать кнопку очистки форм ввода возраста и фио и по новой ввести
            добавить фото результата при удачной симпатии картинка с видом на загс
            если симпатии нет то картинка произвольного вида
        </p>
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
            <p>Жена</p>
            <input type="text" name="wife-name" placeholder="Имя жены" required>
            <input type="number" name="wife-age" placeholder="Возраст жены" required>

            <p>Муж</p>
            <input type="text" name="husband-name" placeholder="Имя мужа" required>
            <input type="number" name="husband-age" placeholder="Возраст мужа" required>

            <button type="submit" value="form-calc">Посчитать</button>
            <button type="submit" value="cache-calc">Посчитать что валяется в кеше</button>
            <input type="reset" name="reset-btn" value="Очистить" required>
        </form>
    <?php } elseif ($result) { ?>
        <h1>Ты победил</h1>
        <img src="/win.png" alt="">
    <?php } else { ?>
        <h1>Ты проиграл</h1>
        <img src="/ultra-win.png" alt="">
    <?php } ?>
</body>

</html>