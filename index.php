<?php
require('persons_array.php');

// Объединение ФИО
// getFullnameFromParts принимает как аргумент три строки — фамилию, имя и отчество. Возвращает как результат их же, но склеенные через пробел.

$surname = 'Иванов';
$name = 'Иван';
$patronomyc = 'Иванович';

function getFullnameFromParts($surname, $name, $patronomyc) {
    return $surname . ' ' . $name . ' ' . $patronomyc;
}
echo getFullnameFromParts($surname, $name, $patronomyc);
echo "<hr>";


// Разбиение ФИО
// getPartsFromFullname принимает как аргумент одну строку — склеенное ФИО. Возвращает как результат массив из трёх элементов с ключами 'surname', 'name', 'patronomyc'.

function getPartsFromFullname($name) {
    $a = ['surname', 'name', 'patronomyc'];
    $b = explode(' ', $name);
    return array_combine($a, $b);
}

foreach ($example_persons_array as $value) {
    $name = $value['fullname'];
    print_r(getPartsFromFullname($name));
    echo "<br>";
}
echo "<hr>";


// Сокращение ФИО
// Функция getShortName, принимает как аргумент строку, содержащую ФИО вида «Иванов Иван Иванович»
// и возвращающую строку вида «Иван И.», где сокращается фамилия и отбрасывается отчество.
// Для разбиения строки на составляющие используется функция getPartsFromFullname.

function getShortName($name) {
    $arr = getPartsFromFullname($name);
    $firstName = $arr['name'];
    $surname = $arr['surname'];
    return $firstName . ' ' . mb_substr($surname, 0, 1) . '.';
}

foreach ($example_persons_array as $value) {
    $name = $value['fullname'];
    echo getShortName($name) . "<br>";
}
echo "<hr>";


// Функция определения пола по ФИО
// Функция getGenderFromName, принимает как аргумент строку, содержащую ФИО (вида «Иванов Иван Иванович»).
// Определение производится следующим образом:
// внутри функции делим ФИО на составляющие с помощью функции getPartsFromFullname;
// изначально «суммарный признак пола» считаем равным 0;
// если присутствует признак мужского пола — прибавляем единицу, если женского — отнимаем единицу.
// после проверок всех признаков, если «суммарный признак пола»:
// больше нуля — возвращаем 1 (мужской пол); меньше нуля — возвращаем -1 (женский пол); равен 0 — возвращаем 0 (неопределенный пол).
// Признаки мужского пола: отчество заканчивается на «ич»; имя заканчивается на «й» или «н»; фамилия заканчивается на «в».
// Признаки женского пола: отчество заканчивается на «вна»; имя заканчивается на «а»; фамилия заканчивается на «ва»;

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

foreach ($example_persons_array as $value) {
    $name = $value['fullname'];
    echo getGenderFromName($name) . "<br>";
}
echo "<hr>";


// Определение возрастно-полового состава
// Функция getGenderDescription принимает как аргумент массив $example_persons_array.
// Как результат функции возвращается информация о гендерном составе аудитории.
// Используется функция фильтрации элементов массива, функция подсчета элементов массива, функция getGenderFromName, округление.

function getGenderDescription($persons) {

    $men = array_filter($persons, function ($persons) {
        $fullname = $persons['fullname'];
        $genderMen = getGenderFromName($fullname);
        if ($genderMen > 0) {
            return $genderMen;
        }
    });

    $women = array_filter($persons, function ($persons) {
        $fullname = $persons['fullname'];
        $genderWomen = getGenderFromName($fullname);
        if ($genderWomen < 0) {
            return $genderWomen;
        }
    });

    $failedGender = array_filter($persons, function ($persons) {
        $fullname = $persons['fullname'];
        $genderFailed = getGenderFromName($fullname);   // 0
        if ($genderFailed == 0) {                       // true
            return $genderFailed + 1;                   // 0 + 1 = 1
            // при этом если "неопределенного пола" не будет, то значение не будет выведено изначально в функции getGenderFromName($fullname)
        }
    });

    // в результате выполнения функции по определению пола - getGenderFromName($name)
    $allMan = count($men);                       // подсчитываются все мужчины
    $allWomen = count($women);                   // подсчитываются все женщины
    $allFailedGender = count($failedGender);     // подсчитываются все чей пол не определился
    $allPiople = $allMan + $allWomen + $allFailedGender;   // можно было записать: $allPiople = count($persons);

    $percentMen = round((100 / $allPiople * $allMan), 0);
    $percentWomen = round((100 / $allPiople * $allWomen), 0);
    $percenFailedGender = round((100 / $allPiople * $allFailedGender), 0);

    echo 'Гендерный состав аудитории:<br>';
    echo '---------------------------<br>';
    echo "Мужчины - $percentMen% <br>";
    echo "Женщины - $percentWomen% <br>";
    echo "Неудалось определить - $percenFailedGender%";
}
getGenderDescription($example_persons_array);
echo "<hr>";


// Идеальный подбор пары
// Функция getPerfectPartner принимает: первые три аргумента - строки с фамилией, именем и отчеством (регистр может быть любым).
// Четвертый аргумент - массив $example_persons_array.
// Алгоритм поиска идеальной пары:
// фамилию, имя, отчество приводятся к нужному регистру;
// функция getFullnameFromParts объединяет ФИО;
// функция getGenderFromName определяет пол для ФИО;
// случайным образом выбирается любой человек в массиве;
// getGenderFromName, проверяет, что выбранное из массива ФИО - противоположного пола, если нет, то возвращаемся к шагу 4, если да - возвращаем информацию.
// Как результат функции возвращается информация совместимости.

// Для записи аргументов в функцию

// $surname = 'ИваНов';
// $name = 'Иван';
// $patronomyc = 'иванович';

// $surname = 'Степанова';
// $name = 'Наталья';
// $patronomyc = 'Степановна';

// $surname = 'Пащенко';
// $name = 'Владимир';
// $patronomyc = 'Александрович';

// $surname = 'Громов';
// $name = 'Александр';
// $patronomyc = 'Иванович';

// $surname = 'Славин';
// $name = 'Семён';
// $patronomyc = 'Сергеевич';

// $surname = 'Цой';
// $name = 'Владимир';
// $patronomyc = 'Антонович';

// $surname = 'Быстрая';
// $name = 'Юлия';
// $patronomyc = 'Сергеевна';

// $surname = 'Шматко';
// $name = 'Антонина';
// $patronomyc = 'Сергеевна';

// $surname = 'аль-Хорезми';
// $name = 'Мухаммад';
// $patronomyc = 'ибн-Муса';

// $surname = 'Бардо';
// $name = 'Жаклин';
// $patronomyc = 'Фёдоровна';

$surname = 'Шварцнегер';
$name = 'Арнольд';
$patronomyc = 'Густавович';

function getPerfectPartner($surname, $name, $patronomyc, $persons) {

    $surnameNorm = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $nameNorm = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronomycNorm = mb_convert_case($patronomyc, MB_CASE_TITLE_SIMPLE);

    $fullNameNorm = getFullnameFromParts($surnameNorm, $nameNorm, $patronomycNorm);  // полное имя главного имени
    $shortNameNorm = getShortName($fullNameNorm);                                    // сокращенное имя главного имени
    $genderFullNameNorm = getGenderFromName($fullNameNorm);                          // пол главного имени в виде: -1 0 1

    if ($genderFullNameNorm == 0) {
        echo "Заданы аргументы неопределенного пола";
        die;
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
    
    echo "$shortNameNorm + $personShortNameRand = <br>";
    echo "♡ Идеально на $percentPerfect% ♡";
}
getPerfectPartner($surname, $name, $patronomyc, $example_persons_array);