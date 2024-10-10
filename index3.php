<?php
require('persons_array.php');

// Функция getPerfectPartner - Идеальный подбор пары
// Аргументы функции $surname, $name, $patronomyc формируются автоматически в случайном порядке
// Проверка аргументов на неопределенный пол сохраняется в функции

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

// Автоматическое формирование аргументов для функции getPerfectPartner 
$allPersonsArray = count($example_persons_array);
$numNameRand = rand(0, $allPersonsArray - 1);
$personRand = $example_persons_array[$numNameRand]['fullname'];

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
    $genderFullNameNorm = getGenderFromName($fullNameNorm);                          // пол главного имени в виде: -1 0 1

    if ($genderFullNameNorm == 0) {
        return "Заданы аргументы неопределенного пола";
    }

    $allPersons = count($persons);

    // проверка противоположности пола
    do {
        $personsNumRand = rand(0, $allPersons - 1);                          // номер случайного имени; отсчет от 0 до 10 = 11; значит от 11-1 будут все значения от 0 до 10
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