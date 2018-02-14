## Настройка
Для использования `FIAS` Измените конфиг `app/config/database.php` под свою БД

Чтобы указать префиксы для удаления, вы можете создать файл `app/config/specifications_local.php`

## Инициализация приложения
`composer install`
```php
<?php
$geo = new \GeoFixer\GeoFixerFacade();
```

Если вы хотите использовать БД ФИАС, рекомендую использовать [этот](https://github.com/Phrlog/yii2-fias) репозиторий для установки бд. После, нужно добавить параметр:

```php
<?php
$geo = new \GeoFixer\GeoFixerFacade($fias = true);
```
Вы можете включить логгирование и задать собственный конфиг для базы данных:
 ```php
 <?php
 $config = [
             'host' => 'localhost',
             'database'  => 'fias',
             'username'  => 'root',
             'password'  => 'password'
         ];
 $geo = new \GeoFixer\GeoFixerFacade($fias = true, $logs = true, $config);
 ```

## Поиск по имеющимся массивам

```php
<?php
/* массив, где нужно найти регион/город */
$find_in = ['Москва', 'Екатеринбург', 'Томск'];

/* регион или город, поступивший от пользователя */
$find_to = 'город Москва';

/* вернется регион или город из массива $find_in,
 максимально подходящий под строку $find_to */
$result = $geo->findSimilarWord($find_to, $find_in);

/* если нужен более строгий отбор, 
то нужно добавить третий параметр */
$result = $geo->findSimilarWord($find_to, $find_in, $strict_search = true);
```

## Поиск по базе ФИАС

### Поиск кода региона
```php
<?php
$region = 'Ленинрадская область';

/* вернется id региона или false */
$code = $geo->findFiasRegion($region);
```

Возможны дополнительные параметры:
```php
<?php
/* кол-во первых букв в регионе, которые должны совпадать */
$first_letters = 2;
$code = $geo->findFiasRegion($region, $first_letters,  $strict_search = true);
```

### Поиск ID города по коду региона
```php
<?php
$first_letters = false;
$strict_search = false;
$full_settlements = true; // поиск не только по городам, но и по поселениям

$city = 'Благовещенск';
$region_code = 28;

$id = $geo->findFiasSettlement($city, $region_code, $first_letters, $strict_search, $full_settlements);
```

### Поиск ID улицы по ID города
```php
<?php
$first_letters = false;
$strict_search = false;

$street = 'Амурская';
$city_id = '8f41253d-6e3b-48a9-842a-25ba894bd093';

$id = $geo->findFiasStreet($street, $city_id, $first_letters, $strict_search);
```

### Поиск ID дома по ID улицы
```php
<?php
$street_id = '3e0d1213-1212-4f87-bdd3-5f8ef6f6473e';
$house = 261;
$building = false; // если нужно, можно указать корпус

$id = $geo->findFiasHouse($house, $street_id, $building);
```
## Поиск по базе КЛАДР

### Поиск кода региона 
```php
<?php
$region = 'Ленинрадская область';

/* вернется id региона или false */
$code = $geo->findKladrRegion($region);
```
### Поиск кода города по коду региона КЛАДР
```php
<?php
$first_letters = false;
$strict_search = false;
$full_settlements = true; // поиск не только по городам, но и по поселениям

$city = 'Благовещенск';
$region_code = 2800000000000;

$id = $geo->findKladrSettlement($city, $region_code, $first_letters, $strict_search, $full_settlements);
```
### Поиск кода улицы по коду города
```php
<?php
$first_letters = false;
$strict_search = false;

$street = 'Амурская';
$city_code = '2800000100000';

$id = $geo->findKladrStreet($street, $city_code, $first_letters, $strict_search);
```

## Тестирование
### Выполнить все тесты:
`vendor/bin/phpunit`
### Выполнить тесты, не требующие бд ФИАС:
`vendor/bin/phpunit --group common`
### Выполнить тесты, использующие бд ФИАС:
`vendor/bin/phpunit --group fias`
