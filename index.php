<?php
//Это только MVP - сильно не осуждать

/**
 * Main page function
 * @param array $body
 * @return bool
 */
function main(array $body): bool
{
    $formHolderNames = [
        'wafu-name',
        'wafu-age',
        'husband-name',
        'husband-age',
    ];

    array_walk($formHolderNames, static function ($name) use ($body): void {
        if (!isset($body[$name]) || empty($body[$name])) {
            throw new Exception('Недопустипо передовать незаполненное поле');
        }
    });

    $accepteableDifference = 10;

    return
        abs(mb_strlen($body['husband-name']) - mb_strlen($body['wafu-name'])) <= $accepteableDifference
        && abs($body['husband-age'] - $body['wafu-age']) <= $accepteableDifference;
}

if (!empty($_POST)) {
    try {
        $result = main($_POST);
        var_dump($_POST);
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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php if (!isset($result) && $_SERVER['REQUEST_METHOD'] === 'GET') {?>

    <h1>ЗАДАНИЕ</h1>
    <pre>
    создать веб форму реализовать игру
    удаленная симпатия которая позволяет определить совместимость людей
    по их имени и позврасту ,если разница в возврасте менее 10 лет и разница суммы
    букв в фио влюбленных менее 10,то они подходят друг другу если условия не выполняются
    то не подходят друг другу
    в этой же странице реализовать многократный перебор спутников
    реализовать кнопку очистки форм ввода возвраста и фио и по новой ввести
    добавить фото результата при удачной симпатии картинка с видом на загс
    если симпатии нет то картинка произвольного вида
    </pre>
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
        <p>Жена</p>
        <label for="">Имя</label>
        <input type="text" name="wafu-name" value="<?=$wafuName ?? null ?>">
        <label for="">возвраста</label>
        <input type="number" name="wafu-age" value="<?=$wafuAge ?? null ?>">

        <p>Муж</p>
        <label for="">Имя</label>
        <input type="text" name="husband-name" value="<?=$husbandName ?? null?>">
        <label for="">возвраста</label>
        <input type="number" name="husband-age" value="<?=$husbandAge ?? null?>">
        <br>
        <input type="submit">
    </form>
    <?php } elseif ($result) {?>
        <h1>Ты победил</h1>
        <img src="/win.png" alt="">
    <?php } else {?>
        <h1>Ты проиграл</h1>
        <img src="/ultra-win.png" alt="">
    <?php } ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
        <input type="submit" value="Попробовать снова">
    </form>
</body>

</html>