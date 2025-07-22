<?php
//TODO: Это только MVP - сильно не осуждать
//TODO: Избаиться от ??
function main(): void
{
    $post = $_POST;

    if (empty($post)) {
        return;
    }

    var_dump($post);
    $formHolderNames = [
        'wafu-name',
        'wafu-age',
        'husband-name',
        'husband-name',
    ];

    array_walk($formHolderNames, static function ($name) use ($post): void {
        if (!isset($_POST[$name])) {
            throw new Exception('Недопустипо передовать незаполненное поле');
        }
    });


    if (count($post)) {

    }

}

main();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
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
</body>

</html>