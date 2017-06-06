# Geofixer
[![Build Status](https://travis-ci.org/Phrlog/GeoFixer.svg?branch=master)](https://travis-ci.org/Phrlog/GeoFixer)
[![Coverage Status](https://coveralls.io/repos/github/Phrlog/GeoFixer/badge.svg)](https://coveralls.io/github/Phrlog/GeoFixer)

## Настройка
Для использования `FIAS` Измените конфиг `app/config/database.php` под свою БД

## Инициализация приложения
`composer install`
```PHP
$geo = new \GeoFixer\GeoFixerFacade();
```

Если вы хотите использовать БД ФИАС рекомендую использовать [этот](https://github.com/Phrlog/yii2-fias) репозиторий для установки бд. После, нужно добавить параметр:

```PHP
$geo = new \GeoFixer\GeoFixerFacade($fias = true);
```

## Поиск по имеющимся массивам

```PHP
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
```

## Поиск кода региона по базе ФИАС
```PHP
$region = 'Ленинрадская область';

/* вернется id региона или false */
$code = $geo->findFiasRegion($region);
```

Возможны дополнительные параметры:
```PHP
/* кол-во первых букв в регионе, которые должны совпадать */
$first_letters = 2;
$code = $geo->findFiasRegion($region, $first_letters,  $strict_search = true);
```

## Поиск ID города по коду региона ФИАС
```PHP
$first_letters = false;
$strict_search = false;
$full_settlements = true; // поиск не только по городам, но и по поселениям

$city = 'Благовещенск';
$region_code = 28;

$id = $geo->findFiasSettlement($city, $region_code, $first_letters, $strict_search, $full_settlements);
```

## Поиск ID улицы по ID города
```PHP
$first_letters = false;
$strict_search = false;

$street = 'Амурская';
$city_id = '8f41253d-6e3b-48a9-842a-25ba894bd093';

$id = $geo->findFiasStreet($street, $city_id, $first_letters, $strict_search);
```

## Поиск ID дома по ID улицы
```PHP
$street_id = '3e0d1213-1212-4f87-bdd3-5f8ef6f6473e';
$house = 261;
$building = false; // если нужно, можно указать корпус

$id = $geo->findFiasHouse($house, $street_id, $building);
```

## Тестирование
### Выполнить все тесты:
`vendor/bin/phpunit`
### Выполнить тесты, не требующие бд ФИАС:
`vendor/bin/phpunit --group common`
### Выполнить тесты, использующие бд ФИАС:
`vendor/bin/phpunit --group fias`
