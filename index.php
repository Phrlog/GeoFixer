<?php

/* Инициализация приложения */

require_once dirname(__FILE__) . '/vendor/autoload.php';

$geo = new \GeoFixer\GeoFixerFacade($fias = true);

/* -------------------------------------------------------------------- */

/* Поиск по имеющимся массивам */

/* массив, где нужно найти регион/город */
$find_in = ['Москва', 'Екатеринбург', 'Томск'];

/* регион или город, поступивший от пользователя */
$find_to = 'город Москва';

/* вернется регион или город из массива $find_in,
 максимально подходящий под строку $find_to */
$result = $geo->findSimilarWord($find_to, $find_in);

/* если нужен более строгий отбор,
то нужно добавить третий параметр */
$result = $geo->findSimilarWord($find_to, $find, $strict_search = true);

/* -------------------------------------------------------------------- */

/** Поиск кода региона по базе ФИАС */

$region = 'Ленинрадская область';

/* вернется id региона или false */
$code = $geo->findFiasRegion($region);

/* Возможны дополнительные параметры: */

/* кол-во первых букв в регионе, которые должны совпадать */
$first_letters = 2;
$code = $geo->findFiasRegion($region, $first_letters,  $strict_search = true);

/* -------------------------------------------------------------------- */

/* Поиск ID города по коду региона ФИАС */

$first_letters = false;
$strict_search = false;
$full_settlements = true; // поиск не только по городам, но и по поселениям

$city = 'Благовещенск';
$region_code = 28;

$id = $geo->findFiasSettlement($city, $region_code, $first_letters, $strict_search, $full_settlements);

/* -------------------------------------------------------------------- */

/* Поиск ID улицы по ID города */

$first_letters = false;
$strict_search = false;

$street = 'Амурская';
$city_id = '8f41253d-6e3b-48a9-842a-25ba894bd093';

$id = $geo->findFiasStreet($street, $city_id, $first_letters, $strict_search);

/* -------------------------------------------------------------------- */

/* Поиск ID дома по ID улицы */

$street_id = '3e0d1213-1212-4f87-bdd3-5f8ef6f6473e';
$house = 261;
$building = false; // если нужно, можно указать корпус

$id = $geo->findFiasHouse($house, $street_id, $building);

/* -------------------------------------------------------------------- */

