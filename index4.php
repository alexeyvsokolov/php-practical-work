<?php
require('persons_array.php');

// Функция getPerfectPartner - Идеальный подбор пары
// Аргументы функции $surname, $name, $patronomyc формируются автоматически в случайном порядке
// Формируется новый массив только определенного пола на основе массива $example_persons_array
// За счет формирования нового массива (без неопределенного пола), проверка аргументов на неопределенный пол исключена из функции

// Объединение ФИО
function getFullnameFromParts($surname, $name, $patronomyc) {
    return $surname . ' ' . $name . ' ' . $patronomyc;
}

// Разбиение ФИО
function getPartsFromFullname($name) {
    $a = ['surname', 'name', 'patronomyc'];
    $b = explode(' ', $name);
    return array_combine($a, $b);
}

// Сокращение ФИО
function getShortName($name) {
    $arr = getPartsFromFullname($name);
    $firstName = $arr['name'];
    $surname = $arr['surname'];
    return $firstName . ' ' . mb_substr($surname, 0, 1) . '.';
}

// Функция определения пола по ФИО
function getGenderFromName($name) {
    $arr = getPartsFromFullname($name);
    $surname = $arr['surname'];
    $firstName = $arr['name'];
    $patronomyc = $arr['patronomyc'];
    $sumGender = 0;

    if (mb_substr($surname, -1, 1) === 'в') {
        $sumGender++;
    } elseif (mb_substr($surname, -2, 2) === 'ва') {
        $sumGender--;
    }

    if ((mb_substr($firstName, -1, 1) == 'й') || (mb_substr($firstName, -1, 1) == 'н')) {
        $sumGender++;
    } elseif (mb_substr($firstName, -1, 1) === 'а') {
        $sumGender--;
    }

    if (mb_substr($patronomyc, -2, 2) === 'ич') {
        $sumGender++;
    } elseif (mb_substr($patronomyc, -3, 3) === 'вна') {
        $sumGender--;
    }

    return ($sumGender <=> 0);
}

// Массив только определенного пола на основе массива $example_persons_array
$new_persons_array = array_filter($example_persons_array, function ($example_persons_array) {
    if (getGenderFromName($example_persons_array['fullname']) !== 0) {
        return $example_persons_array;
    }
});

// Автоматическое формирование аргументов для функции getPerfectPartner 
$numNameRand = array_rand($new_persons_array, 1);             // выбираем один случайный ключ массива
$personRand = $new_persons_array[$numNameRand]['fullname'];   // получаем случайное имя массива

$surname = getPartsFromFullname($personRand)['surname'];
$name = getPartsFromFullname($personRand)['name'];
$patronomyc = getPartsFromFullname($personRand)['patronomyc'];

// Идеальный подбор пары
function getPerfectPartner($surname, $name, $patronomyc, $persons) {

    $surnameNorm = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $nameNorm = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronomycNorm = mb_convert_case($patronomyc, MB_CASE_TITLE_SIMPLE);

    $fullNameNorm = getFullnameFromParts($surnameNorm, $nameNorm, $patronomycNorm);  // полное имя главного имени
    $shortNameNorm = getShortName($fullNameNorm);                                    // сокращенное имя главного имени
    $genderFullNameNorm = getGenderFromName($fullNameNorm);                          // пол главного имени в виде: -1 или 1

    // проверка противоположности пола
    do {
        $personsNumRand = array_rand($persons);
        $personFullNameRand = $persons[$personsNumRand]['fullname'];         // полное имя случайного имени
        $personFullNameRandGender = getGenderFromName($personFullNameRand);  // пол случайного имени в виде: -1 0 1
    } while (($genderFullNameNorm == $personFullNameRandGender) || ($personFullNameRandGender == 0));

    $personShortNameRand = getShortName($personFullNameRand);   // сокращенное имя случайного имени
    $percentPerfect = rand(5000, 10000) / 100;                  // от 50% до 100%

    return <<< HEREDOC
    $shortNameNorm + $personShortNameRand =
    ♡ Идеально на $percentPerfect% ♡
    HEREDOC;
}
echo getPerfectPartner($surname, $name, $patronomyc, $example_persons_array) . PHP_EOL;