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
 * Finds potential pairs from given arrays of available husbands and wives.
 *
 * @param array{
 *     free_husband: array<array-key, array{name: string, age: int}>,
 *     free_wife: array<array-key, array{name: string, age: int}>
 * } $people Associative array containing two sub-arrays of potential candidates
 *
 * @return PersonalDataDto[] Returns an array of possible pair combinations
 *
 * @throws InvalidArgumentException If input arrays don't match expected structure
 */
function findPotentialPair(array $people): array
{
    if (count($people) === 0) {
        return [];
    }

    $pairs = [];

    foreach ($people['free_wife'] as $wife) {
        foreach ($people['free_husbands'] as $husband) {
            $wife = new PersonalDataDto($wife['name'], $wife['age']);
            $husband = new PersonalDataDto($husband['name'], $husband['age']);
            if (checkPairCompatibility($wife, $husband)) {
                $pairs[] = ['wife' => $wife, 'husband' => $husband];
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

    array_walk($formHolderNames, static function ($name, $type) use ($body): void {
        if (!isset($body[$name]) || empty($body[$name]) && gettype($body[$name]) === $type) {
            throw new ValidationException('Недопустимо передавать незаполненное поле');
        }
    });

}

if (!empty($_POST)) {

    $body = $_POST;
    gameFormValidation($body);

    $wife = new PersonalDataDto($body['wife-name'], $body['wife-name']);
    $husband = new PersonalDataDto($body['husband-name'], $body['husband-name']);

    try {
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
        * {
            color: #fe7f2d;
            background-color: #233d4d;
            font-family: Verdana, Geneva, sans-serif;
        }

        input {
            margin-top: 0.5rem;
            outline-width: 0;
        }

        input::placeholder {
            font-weight: bold;
            opacity: 0.5;
            color: #fe7f2d;
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
            <input type="text" name="wife-name" placeholder="Имя жены">
            <input type="number" name="wife-age" placeholder="Возраст жены">

            <p>Муж</p>
            <input type="text" name="husband-name" placeholder="Имя мужа">
            <input type="number" name="husband-age" placeholder="Возраст мужа">

            <button type="submit" value="form-calc">Проверить совместимость</button>
            <button type="submit" value="cache-calc">Рассчитать совместимость бедолаг из кеша</button>
            <input type="reset" name="reset-btn" value="Очистить">
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