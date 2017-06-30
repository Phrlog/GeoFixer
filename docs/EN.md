## Setting
To use `FIAS` Change the config `app/config/database.php` to your database

To specify prefixes for deletion, you can change the config `app/config/specifications.php`

## Initializing the application
`composer install`
```php
<?php
$geo = new \GeoFixer\GeoFixerFacade();
```
If you want to use the FIAS database, I recommend using [this](https://github.com/Phrlog/yii2-fias) a repository to install the DB. After, you need to add the parameter:
```php
<?php
$geo = new \GeoFixer\GeoFixerFacade($fias = true);
```

## Search by available arrays

```php
<?php
/* array where you need to find a word */
$find_in = ['Москва', 'Екатеринбург', 'Томск'];

/* word received from the user */
$find_to = 'город Москва';

/* the most suitable word for the string $find_to returns from the $find_in array */
$result = $geo->findSimilarWord($find_to, $find_in);

/* if more rigorous selection is needed,
   Then you need to add a third parameter */
$result = $geo->findSimilarWord($find_to, $find_in, $strict_search = true);
```

## Search by FIAS database

### Search for region code
```php
<?php
$region = 'Ленинрадская область';

/* will return the region id or false */
$code = $geo->findFiasRegion($region);
```

Additional parameters are possible:
```php
<?php
/* the number of first letters in the region that must match */
$first_letters = 2;
$code = $geo->findFiasRegion($region, $first_letters,  $strict_search = true);
```

### City ID search by region code
```php
<?php
$first_letters = false;
$strict_search = false;
$full_settlements = true; // search not only for cities, but also for settlements

$city = 'Благовещенск';
$region_code = 28;

$id = $geo->findFiasSettlement($city, $region_code, $first_letters, $strict_search, $full_settlements);
```

### ID search by city ID
```php
<?php
$first_letters = false;
$strict_search = false;

$street = 'Амурская';
$city_id = '8f41253d-6e3b-48a9-842a-25ba894bd093';

$id = $geo->findFiasStreet($street, $city_id, $first_letters, $strict_search);
```

### Search for a house ID by street ID
```php
<?php
$street_id = '3e0d1213-1212-4f87-bdd3-5f8ef6f6473e';
$house = 261;
$building = false; // if necessary, you can specify the body of building

$id = $geo->findFiasHouse($house, $street_id, $building);
```
## Search by database KLADR

### Search for region code 
```php
<?php
$region = 'Ленинрадская область';

/* will return the region id or false */
$code = $geo->findKladrRegion($region);
```
### Search for a city code by region code
```php
<?php
$first_letters = false;
$strict_search = false;
$full_settlements = true; // search not only for cities, but also for settlements

$city = 'Благовещенск';
$region_code = 2800000000000;

$id = $geo->findKladrSettlement($city, $region_code, $first_letters, $strict_search, $full_settlements);
```
### Street code search by city code
```php
<?php
$first_letters = false;
$strict_search = false;

$street = 'Амурская';
$city_code = '2800000100000';

$id = $geo->findKladrStreet($street, $city_code, $first_letters, $strict_search);
```

## Tests
### Run all tests:
`vendor/bin/phpunit`
### Run tests that do not require a FIAS database:
`vendor/bin/phpunit --group common`
### Run tests using the FIAS database:
`vendor/bin/phpunit --group fias`