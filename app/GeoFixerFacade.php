<?php
namespace GeoFixer;

use GeoFixer\models\GeoFixer;
use GeoFixer\models\DatabaseConnection;

use Katzgrau\KLogger;
use Exception;

/**
 * Class GeoFixerFacade
 *
 * @package GeoFixer
 */
class GeoFixerFacade
{
    /**
     * @var KLogger\Logger
     */
    private $logger;

    /**
     * @var GeoFixer
     */
    private $geo;

    /**
     * Инициализируем (если передан параметр) БД и логирование
     *
     * GeoFixerFacade constructor.
     * @param bool $fias
     * @param bool $logger
     * @param array $config
     */
    public function __construct($fias = false, $logger = false, $config = null)
    {
        if ($logger !== false) {
            $this->logger = new KLogger\Logger(dirname(dirname(__FILE__)) . '/logs');
        } else {
            $this->logger = false;
        }

        if ($fias === true) {
            if ($config == null) {
                $config = include 'config/database.php';
            }

            try {
                DatabaseConnection::makeConnection($config);
            } catch (Exception $e) {
                $this->logger->error('Exception: ' . $e->getMessage());
            }
        }

        $this->geo = new GeoFixer();
    }

    /**
     * Поиск похожих слов в массиве
     * Логирование ошибок
     *
     * @param $word
     * @param $search_array
     * @param bool $strict_search
     *
     * @return string|false
     */
    public function findSimilarWord($word, $search_array, $strict_search = false)
    {
        $this->geo->isStrict($strict_search);

        $result = $this->geo->findSimilarWord($word, $search_array);

        if ($result === false && $this->logger) {
            $this->logger->warning('Не найдено похожее слово: ' . $word);
            $this->logger->warning('Строгий режим: ' . (int) $strict_search);
            $this->logger->warning('Массива для поиска: ' . implode($search_array, ', ') . PHP_EOL);
        }

        return $result;
    }

    /**
     * Поиск кода региона в базе КЛАДР
     * Логирование ошибок
     *
     * @param $region
     * @param bool $first_letters
     * @param bool $strict_search
     *
     * @return string|false
     */
    public function findKladrRegion($region, $first_letters = false, $strict_search = false)
    {
        $this->geo = new GeoFixer();

        $result = $this->findFiasRegion($region, $first_letters, $strict_search);

        if ($result !== false) {
            return str_pad($result, 13, '0');
        }

        return $result;
    }

    /**
     * Поиск кода региона в базе ФИАС
     * Логирование ошибок
     *
     * @param $region
     * @param bool $first_letters
     * @param bool $strict_search
     *
     * @return string|false
     */
    public function findFiasRegion($region, $first_letters = false, $strict_search = false)
    {
        $this->geo = new GeoFixer();

        $this->geo->isStrict($strict_search);
        $this->geo->isFirstLetters($first_letters);

        $result = $this->geo->findFiasRegion($region);

        if ($result === false && $this->logger) {
            $this->logger->warning('Не найден регион ' . $region . ' в базе ФИАС');
            $this->logger->warning('Строгий режим: ' . (int) $strict_search);
            $this->logger->warning('Режим "совпадают первые буквы": ' . (int) $first_letters . PHP_EOL);
        }

        return $result;
    }

    /**
     * Поиск ID городов, или ID городов и поселений по коду региона в базе ФИАС
     * Логирование ошибок
     *
     * @param $city
     * @param $region_code
     * @param bool $first_letters
     * @param bool $strict_search
     *
     * @return string|false
     */
    public function findFiasSettlement($city, $region_code, $first_letters = false, $strict_search = false, $full_settlements = false)
    {
        $this->geo = new GeoFixer();

        $this->geo->isStrict($strict_search);
        $this->geo->isFirstLetters($first_letters);
        $this->geo->isFullSettlements($full_settlements);

        $result = $this->geo->findFiasSettlements($city, $region_code);

        if ($result === false && $this->logger) {
            $this->logger->warning('Не найден город ' . $city . ' в регионе с кодом ' . $region_code . ' базы ФИАС');
            $this->logger->warning('Строгий режим: ' . (int) $strict_search);
            $this->logger->warning('Режим "совпадают первые буквы": ' . (int) $first_letters . PHP_EOL);
        }

        return $result;
    }

    /**
     * Поиск ID городов, или ID городов и поселений по коду региона в базе КЛАДР
     * Логирование ошибок
     *
     * @param $city
     * @param $region_code
     * @param bool $first_letters
     * @param bool $strict_search
     *
     * @return string|false
     */
    public function findKladrSettlement($city, $region_code, $first_letters = false, $strict_search = false, $full_settlements = false)
    {
        $this->geo = new GeoFixer();

        $this->geo->isStrict($strict_search);
        $this->geo->isFirstLetters($first_letters);
        $this->geo->isFullSettlements($full_settlements);

        $region_code = substr($region_code, 0, 2);

        $result = $this->geo->findKladrSettlements($city, $region_code);

        if ($result === false && $this->logger) {
            $this->logger->warning('Не найден город ' . $city . ' в регионе с кодом ' . $region_code . ' базы ФИАС');
            $this->logger->warning('Строгий режим: ' . (int) $strict_search);
            $this->logger->warning('Режим "совпадают первые буквы": ' . (int) $first_letters . PHP_EOL);
        }

        return $result;
    }

    /**
     * Поиск ID улицы по ID города в базе ФИАС
     * Логирование ошибок
     *
     * @param $street
     * @param $city_id
     * @param bool $first_letters
     * @param bool $strict_search
     *
     * @return string|false
     */
    public function findFiasStreet($street, $city_id, $first_letters = false, $strict_search = false)
    {
        $this->geo = new GeoFixer();

        $this->geo->isStrict($strict_search);
        $this->geo->isFirstLetters($first_letters);

        $result = $this->geo->findFiasStreets($street, $city_id);

        if ($result === false && $this->logger) {
            $this->logger->warning('Не найдена улица ' . $street . ' в городе с id ' . $city_id . ' базы ФИАС');
            $this->logger->warning('Строгий режим: ' . (int) $strict_search);
            $this->logger->warning('Режим "совпадают первые буквы": ' . (int) $first_letters . PHP_EOL);
        }

        return $result;
    }

    /**
     * Поиск кода улицы по коду города в базе КЛАДР
     * Логирование ошибок
     *
     * @param $street
     * @param $city_code
     * @param bool $first_letters
     * @param bool $strict_search
     *
     * @return string|false
     */
    public function findKladrStreet($street, $city_code, $first_letters = false, $strict_search = false)
    {
        $this->geo = new GeoFixer();

        $this->geo->isStrict($strict_search);
        $this->geo->isFirstLetters($first_letters);

        $result = $this->geo->findKladrStreets($street, $city_code);

        if ($result === false && $this->logger) {
            $this->logger->warning('Не найдена улица ' . $street . ' в городе с кодом ' . $city_code . ' базы КЛАДР');
            $this->logger->warning('Строгий режим: ' . (int) $strict_search);
            $this->logger->warning('Режим "совпадают первые буквы": ' . (int) $first_letters . PHP_EOL);
        }

        return $result;
    }


    /**
     * Поиск ID дома по ID улицы в базе ФИАС
     *
     * @param $house
     * @param $street_id
     * @param bool $building
     *
     * @return string|false
     */
    public function findFiasHouse($house, $street_id, $building = false)
    {
        $this->geo = new GeoFixer();

        $result = $this->geo->findFiasHouses($house, $street_id, $building);

        if ($result === false && $this->logger) {
            if ($building) {
                $this->logger->warning('Не найден дом ' . $house . ', корпус ' . $building . ' на улице с id ' . $street_id . ' базы ФИАС' . PHP_EOL);
            } else {
                $this->logger->warning('Не найден дом ' . $house . ' на улице с id ' . $street_id . ' базы ФИАС: ' . $house . PHP_EOL);
            }
        }

        return $result;
    }
}
