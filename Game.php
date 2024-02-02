<?php
session_start();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//ЗАДАНИЕ
/*
    создать веб форму реализовать игру
    удаленная симпатия которая позволяет определить совместимость людей
    по их имени и позврасту ,если разница в возврасте менее 10 лет и разница суммы
    букв в фио влюбленных менее 10,то они подходят друг другу если условия не выполняются
    то не подходят друг другу
    в этой же странице реализовать многократный перебор спутников
    реализовать кнопку очистки форм ввода возвраста и фио и по новой ввести
    добавить фото результата при удачной симпатии картинка с видом на загс
    если симпатии нет то картинка произвольного вида
*/


//Переменные для сохранение данных на форме при отправке на сервер
$womanAgeInput = $_GET['womanAgeInput'];
$manAgeInput = $_GET['manAgeInput'];
$womanNameInput = $_GET['womanNameInput'];
$manNameInput = $_GET['manNameInput'];


//Массив с людьми не нашедшеми себе пару
if(!isset($_SESSION["notMatchedPeople"] ))
        $_SESSION["notMatchedPeople"] = [];

//Переменная отвечающая за скрытость кнопки
if (!$_SESSION["notMatchedPeople"])
    $buttonVisible = "hideButton";


//условие которое заставляет срабатывать код только по нажатию на кнопку "проверить"
if (!is_null($_GET['send'])) {

    //результ для вывода результата
    $result = "";
    //Переменная для проверки правильности введённых значений
    $checker = true;
    //Переменная для картинки
    $image = "";

    //Расчёт длинны фио молодожёнов
    $manNameLength = mb_strlen($_GET['manNameInput']);
    $womanNameLength = mb_strlen($_GET['womanNameInput']);

    //Регулярные вырожения для проверки имени и возраста на правильность
    $fioPattern = "/^(([A-Za-z]+\s?){2,3})|((([А-Яа-яЁёьъЧч])+\s?){2,3})$/";
    $agePattern = "/^\d+$/";


    //Проверка правильности ввода значений
    if (!preg_match($fioPattern, $_GET['womanNameInput'])) {
        $result .= "Ошибка: ФИО супруги введено не верно <br>";
        $checker = false;
    }
    if (!preg_match($fioPattern, $_GET['manNameInput'])) {
        $result .= "Ошибка: ФИО супруга введено не верно <br>";
        $checker = false;
    }
    if (!preg_match($agePattern, $_GET['womanAgeInput'])) {
        $result .= "Ошибка: Возраст супруги введен не верно <br>";
        $checker = false;
    }
    if (!preg_match($agePattern, $_GET['manAgeInput'])) {
        $result .= "Ошибка: Возраст супруга введен не верно <br>";
        $checker = false;
    }
    if ($checker and $_GET['womanAgeInput'] < 18 and $_GET['manAgeInput'] < 18) {
        $result = "Обоим супругам меньше 18 - по закону РФ такой брак не возможен <br>";
        $checker = false;
    }
    if ($checker and $_GET['womanAgeInput'] < 18) {
        $result = "Супруге меньше 18 - по закону РФ такой брак не возможен <br>";
        $checker = false;
    }
    if ($checker and $_GET['manAgeInput'] < 18) {
        $result = "Супругу меньше 18 - по закону РФ такой брак не возможен <br>";
        $checker = false;
    }


    //Нахождение модуля разности длинны ФИО супругов
    $DiffAgeLength = (int)$_GET['womanAgeInput'] - (int)$_GET['manAgeInput'];
    $DiffAgeLength = Abs($DiffAgeLength);
    //Нахождение модуля разности возраста супругов
    $DiffNameLength = $manNameLength - $womanNameLength;
    $DiffNameLength = Abs($DiffNameLength);
    //Вывод если супруги подходят друг другу
    if ($DiffNameLength < 10 and $DiffAgeLength < 10 and $checker) {
        $result = "<h1>В загс</h1>";
        $image = "Zags.png";
    } //Вывод если супруги не подходят друг другу
    else {
        $result .= "<h1>не в загс</h1>";
        $image = "neZags.png";

        //Заполнение массива с супругом
        global $_GLOBAL;
        $_GLOBAL ["notMatchedMale"] = array("FIO" => $_GET['manNameInput'], "Age" => (int)$_GET['manAgeInput']);
        global $notMatchedFemale;
        $notMatchedFemale = array("FIO" => $_GET['womanNameInput'], "Age" => (int)$_GET['womanAgeInput']);
        global $_SESSION;

        $buttonVisible = "";

        array_push($_SESSION["notMatchedPeople"], $_GLOBAL ["notMatchedMale"]);
        array_push($_SESSION["notMatchedPeople"], $notMatchedFemale);
    }
    print_r( $_SESSION["notMatchedPeople"]);
    $image = '<img src="image/' . $image . '"/>';
}



//Если нажали кнопку "Подобрать пару"
if (!is_null($_GET['secondSend'])) {

    print_r("<br>".$_SESSION["notMatchedPeople"]);
    $notMatchedPeople = $_SESSION["notMatchedPeople"];

    $flag = true;
    if (!$_SESSION["notMatchedPeople"]) {
        $result = "потонцеальных супругов нет. кнопкой можно воспользоваться только если есть супруги которые за время сессии не подошли друг другу";
        $flag = false;
    }

    if ($flag) {

        //Псевдоним для удобства в обращении к массиву
        $flagLove = false;

        $res = [];
        for ($i = 0; $i <= sizeof($_SESSION["notMatchedPeople"]) - 2; $i += 2) {
            for ($j = 1; $i <= sizeof($_SESSION["notMatchedPeople"]) - 1; $i += 2) {

                print_r($notMatchedPeople[$i]);
                print_r($notMatchedPeople[$j]);
                if (mb_strlen($notMatchedPeople[$i]["FIO"]) == mb_strlen($notMatchedPeople[$j]["FIO"]) and $notMatchedPeople[$i]["Age"] == $notMatchedPeople[$j]["Age"]) {
                    $flagLove = true;
                    $buttonVisible = "";
                    $result = $notMatchedPeople[$i]["FIO"] . " и " . $notMatchedPeople[$j]["FIO"] . " могли бы стать отличной парой<br>";
                }
            }

        }
        if (!$flagLove) {
            $result = "Возможныйх пар нет<br>";
        }
    }
}

//условие которое заставляет срабатывать код только по нажатию на кнопку "очистить"
if (!is_null($_GET['reset'])) {
    //очищает поля
    $image = "";
    $womanAgeInput = "";
    $manAgeInput = "";
    $womanNameInput = "";
    $manNameInput = "";
    unset($_GET);
    unset($_SESSION["notMatchedPeople"]);
}


?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="Style.css" rel="stylesheet">
</head>
<body>


<form action="<?php $_SERVER['PHP_SELF'] ?>">

    ФИО
    <br/>
    <label for="wNI">Жены:
        <input type="text" name="womanNameInput" class="type-1" value="<?php echo $womanNameInput ?>"/>
    </label>
    <label>Мужа:
        <input type="text" name="manNameInput" class="type-1" value="<?php echo $manNameInput ?>"/>
    </label>
    <br/>
    Возраст
    <br/>
    <label>Жены:
        <input type="text" name="womanAgeInput" class="type-1" value="<?php echo $womanAgeInput ?>"/>
    </label>
    <label>
        Мужа:
        <input type="text" name="manAgeInput" class="type-1" value="<?php echo $manAgeInput ?>"/>
    </label>
    <br/>

    <input type="submit" name="send" class="c-button" value="Расчитать совместимость"/>
    <input type="submit" name="secondSend" class="c-button" id="<?php echo $buttonVisible; ?>" value="Найти возможные пары"/>
    <input type="submit" name="reset" class="c-button" value="Очистить"/>
</form>
<hr>

<div id="res"><?php echo $result; ?></div>
<div><?php echo $image ?></div>


</body>
</html>