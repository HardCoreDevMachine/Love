<?php
//Это только MVP - сильно не осуждать
var_dump(['post' => $_POST]);
/**
 * Check potential couple compatibility
 *
 * @todo Replace array with a proper DTO object for better type safety and structure.
 *
 * @param array{
 *     "wife-name": string,
 *     "wife-age": int,
 *     "husband-name": string,
 *     "husband-age": int
 * } $pairData Associative array containing partner data.
 * @return bool Returns `true` on success, `false` on failure.
 */
function checkPairCompatibility(array $pairData): bool
{
    $acceptableDifference = 10;

    return
        abs(mb_strlen($pairData['husband-name']) - mb_strlen($pairData['wife-name'])) <= $acceptableDifference
        && abs($pairData['husband-age'] - $pairData['wife-age']) <= $acceptableDifference;
}

/**
 * Finds potential pairs from given arrays of available husbands and wives.
 *
 * @todo Replace array structures with DTOs for better type safety and readability.
 *       Example: Use `Husband[]` and `Wife[]` instead of raw arrays.
 *
 * @param array{
 *     free_husband: array<array-key, array{name: string, age: int}>,
 *     free_wife: array<array-key, array{name: string, age: int}>
 * } $people Associative array containing two sub-arrays of potential candidates
 *
 * @return array<array{
 *     wife-name: string,
 *     wife-age: int,
 *     husband-name: string,
 *     husband-age: int
 * }> Returns an array of possible pair combinations
 *
 * @throws InvalidArgumentException If input arrays don't match expected structure
 */
function findPotentialPair(array $people): array
{
    if (count($people) === 0) {
        return [];
    }

    $result = [];

    foreach ($people['free_wife'] as $wife) {
        foreach ($people['free_husbands'] as $husband) {
            if (checkPairCompatibility([...$wife, ...$husband])) {
                $result[] = [...$wife, ...$husband];
            }
        }
    }

    return $result;
}

/**
 * Summary of gameFormValidation
 *
 * @todo Create another validation exception
 *
 * @param array $body
 * @return void
 *
 * @throws Exception
 */
function gameFormValidation(array $body): void
{
    $formHolderNames = [
        'wife-name',
        'wife-age',
        'husband-name',
        'husband-age',
    ];

    array_walk($formHolderNames, static function ($name) use ($body): void {
        if (!isset($body[$name]) || empty($body[$name])) {
            throw new Exception('Недопустимо передавать незаполненное поле');
        }
    });

}

if (!empty($_POST)) {

    $body = $_POST;
    gameFormValidation($body);
    try {
        $result = checkPairCompatibility($body);
    } catch (Exception $e) {
        http_response_code(400);
        echo $e->getMessage();
        die;
    }

    if (!$result) {
        session_start();

        $_SESSION['free_husbands'][] = [
            'name' => $_POST['husband-name'],
            'age' => $_POST['husband-age'],
        ];
        $_SESSION['free_wife'][] = [
            'name' => $_POST['wife-name'],
            'age' => $_POST['wife-age'],
        ];

        session_write_close();
    }
    var_dump($_SESSION);
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