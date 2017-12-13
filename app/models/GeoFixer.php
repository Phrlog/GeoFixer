<?php

namespace GeoFixer\models;

use GeoFixer\models\queries\AbstractDatabaseQuery;
use GeoFixer\models\queries\RegionsDatabaseQuery;
use GeoFixer\models\queries\SettlementsDatabaseQuery;
use GeoFixer\models\queries\StreetsDatabaseQuery;
use GeoFixer\models\queries\HousesDatabaseQuery;
use GeoFixer\helpers\StringHelper;
use GeoFixer\helpers\FuzzySearchHelper;

/**
 * Class GeoFixer
 *
 * @package GeoFixer\models
 */
class GeoFixer
{
    protected $strict = false;
    protected $first_letters = false;
    protected $full_settlements = false;

    protected $geo_with_ids = [];
    protected $geo_titles = [];
    protected $title_name;
    protected $code_name;

    protected $string_helper;
    protected $fuzzy_helper;

    /**
     * GeoFixer construct
     */
    public function __construct()
    {
        $this->string_helper = new StringHelper();
        $this->fuzzy_helper = new FuzzySearchHelper();

        $this->title_name = AbstractDatabaseQuery::TITLE;
        $this->code_name = AbstractDatabaseQuery::FIAS_CODE;
    }

    /**
     * @param $region
     *
     * @return bool|mixed
     */
    public function findFiasRegion($region)
    {
        $this->code_name = AbstractDatabaseQuery::KLADR_CODE;

        $regions = new RegionsDatabaseQuery();
        $regions = $regions->getRegions();

        if (is_integer($this->first_letters)) {
            $regions = $regions->firstLetters(substr($region, 0, $this->first_letters));
        }

        $regions = $regions->findAll();

        return $this->getResult($regions, $region);
    }

    /**
     * @param $word
     * @param $search_array
     *
     * @return string|false|null
     */
    public function findSimilarWord($word, $search_array)
    {
        if (in_array($word, $search_array)) {
            return $word;
        }
        $this->fuzzy_helper = new FuzzySearchHelper();

        $word = $this->string_helper->wordTranslit($word);

        $translited_words = $this->string_helper->arrayTranslit($search_array);

        $result = $this->strict === true ?
            $this->fuzzy_helper->findBestMatch($word, $translited_words) :
            key($this->fuzzy_helper->findMostSimilarWords($word, $translited_words));

        return $result;
    }

    /**
     * @param $city
     * @param $region_code
     *
     * @return bool|mixed
     */
    public function findFiasSettlements($city, $region_code)
    {
        $this->code_name = AbstractDatabaseQuery::FIAS_CODE;

        $settlements = new SettlementsDatabaseQuery();
        $settlements = $settlements->getSettlements()->regionCode($region_code)->addressLevel($this->full_settlements);

        if (is_integer($this->first_letters)) {
            $settlements = $settlements->firstLetters(substr($city, 0, $this->first_letters));
        }

        $settlements = $settlements->findAll();

        return $this->getResult($settlements, $city);
    }

    /**
     * @param $city
     * @param $region_code
     *
     * @return bool|mixed
     */
    public function findKladrSettlements($city, $region_code)
    {
        $this->code_name = AbstractDatabaseQuery::KLADR_CODE;

        $settlements = new SettlementsDatabaseQuery();
        $settlements = $settlements->getSettlements()->regionCode($region_code)->addressLevel($this->full_settlements);

        if (is_integer($this->first_letters)) {
            $settlements = $settlements->firstLetters(substr($city, 0, $this->first_letters));
        }

        $settlements = $settlements->findAll();

        return $this->getResult($settlements, $city);
    }

    /**
     * @param $street
     * @param $city_id
     *
     * @return bool|mixed
     */
    public function findFiasStreets($street, $city_id)
    {
        $this->code_name = AbstractDatabaseQuery::FIAS_CODE;

        $streets = new StreetsDatabaseQuery();
        $streets = $streets->getStreets()->parentId($city_id)->addressLevel();

        if (is_integer($this->first_letters)) {
            $streets = $streets->firstLetters(substr($street, 0, $this->first_letters));
        }

        $streets = $streets->findAll();

        return $this->getResult($streets, $street);
    }

    /**
     * @param $street
     * @param $city_code
     *
     * @return bool|mixed
     */
    public function findKladrStreets($street, $city_code)
    {
        $this->code_name = AbstractDatabaseQuery::KLADR_CODE;

        $streets = new StreetsDatabaseQuery();
        $city = new SettlementsDatabaseQuery();
        $city_id = $city->getSettlements()->addressLevel(true)->kladrCode($city_code)->findOne();

        if ($city_id === false) {
            return false;
        }

        $city_id = $city_id['address_id'];
        $streets = $streets->getStreets()->parentId($city_id)->addressLevel();

        if (is_integer($this->first_letters)) {
            $streets = $streets->firstLetters(substr($street, 0, $this->first_letters));
        }

        $streets = $streets->findAll();

        return $this->getResult($streets, $street);
    }

    /**
     * @param $house
     * @param $street_id
     *
     * @return bool
     */
    public function findFiasHouses($house, $street_id, $building = false)
    {
        $house_id = new HousesDatabaseQuery();
        $house_id = $house_id->getHouses()->addressId($street_id)->houseNumber($house);

        if ($building !== false) {
            $house_id = $house_id->building($building);
        }

        $house_id = $house_id->findOne();

        return $house_id ? $house_id['house_id'] : false;
    }

    /**
     * Включаем строгий режим поиска
     *
     * @param bool $strict
     */
    public function isStrict($strict = false)
    {
        $this->strict = $strict;
    }


    /**
     * Сколько первых букв должны совпадать при поиске по базам ФИАС
     *
     * (теоретически, снизит кол-во слов, которые придется обрабатывать алгоритмом и тем самым увеличит скорость работы, но может не найти слово, если первые буквы не совпадают
     * из-за опечатки или префиксов)
     *
     * @param int|bool $count
     */
    public function isFirstLetters($count = false)
    {
        if (is_int($count)) {
            $this->first_letters = $count;
        }
    }

    /**
     * Только города, или города и поселения
     *
     * @param bool $is_full
     */
    public function isFullSettlements($is_full = false)
    {
        $this->full_settlements = $is_full;
    }

    /**
     * @param $geo_array
     */
    protected function geoDataHandler($geo_array)
    {
        $this->geo_with_ids[$geo_array[$this->title_name]] = $geo_array[$this->code_name];
        $this->geo_titles[] = $geo_array[$this->title_name];
    }

    /**
     * @param $geo_array
     * @param $word
     * @return bool|mixed
     */
    protected function getResult($geo_array, $word)
    {
        array_map(array($this, 'geoDataHandler'), $geo_array);

        $result = $this->findSimilarWord($word, $this->geo_titles);

        return $result ? $this->geo_with_ids[$result] : false;
    }
}

