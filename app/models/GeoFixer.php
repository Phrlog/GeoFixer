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

        array_map(array($this, 'geoDataHandler'), $regions);

        $result = $this->findSimilarWord($region, $this->geo_titles);

        return $result ? $this->geo_with_ids[$result] : false;
    }

    /**
     * @param $word
     * @param $search_array
     *
     * @return string
     */
    public function findSimilarWord($word, $search_array)
    {
        if (in_array($word, $search_array)) {
            return $word;
        }

        $word = $this->string_helper->wordTranslit($word);

        $translited_words = $this->string_helper->arrayTranslit($search_array);

        if ($this->strict == true) {
            $result = $this->fuzzy_helper->findBestMatch($word, $translited_words);
            return $result;
        }

        $result = key($this->fuzzy_helper->findMostSimilarWords($word, $translited_words));

        return $result ? $result : false;
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
        $settlements = $settlements->getSettlements()->regionCode($region_code)->addressLevel();

        if (is_integer($this->first_letters)) {
            $settlements = $settlements->firstLetters(substr($city, 0, $this->first_letters));
        }

        $settlements = $settlements->findAll();

        array_map(array($this, 'geoDataHandler'), $settlements);

        $result = $this->findSimilarWord($city, $this->geo_titles);

        return $result ? $this->geo_with_ids[$result] : false;
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
        $settlements = $settlements->getSettlements()->regionCode($region_code)->addressLevel();

        if (is_integer($this->first_letters)) {
            $settlements = $settlements->firstLetters(substr($city, 0, $this->first_letters));
        }

        $settlements = $settlements->findAll();

        array_map(array($this, 'geoDataHandler'), $settlements);

        $result = $this->findSimilarWord($city, $this->geo_titles);

        if (!$result) {
            return false;
        }
        if (is_null($this->geo_with_ids[$result])) {
            return false;
        }

        return $this->geo_with_ids[$result];
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

        array_map(array($this, 'geoDataHandler'), $streets);

        $result = $this->findSimilarWord($street, $this->geo_titles);

        return $result ? $this->geo_with_ids[$result] : false;
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
        if ($city_id) {
            $city_id = $city_id['address_id'];
        } else {
            return false;
        }
        $streets = $streets->getStreets()->parentId($city_id)->addressLevel();

        if (is_integer($this->first_letters)) {
            $streets = $streets->firstLetters(substr($street, 0, $this->first_letters));
        }

        $streets = $streets->findAll();

        array_map(array($this, 'geoDataHandler'), $streets);

        $result = $this->findSimilarWord($street, $this->geo_titles);

        if (!$result) {
            return false;
        }
        if (is_null($this->geo_with_ids[$result])) {
            return false;
        }

        return $this->geo_with_ids[$result];
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

        if ($building != false) {
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
     * @param bool $count
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
}

