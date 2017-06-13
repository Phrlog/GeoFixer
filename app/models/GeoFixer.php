<?php

namespace GeoFixer\models;

use GeoFixer\models\queries\RegionsDatabaseQuery;
use GeoFixer\models\queries\SettlementsDatabaseQuery;
use GeoFixer\models\queries\StreetsDatabaseQuery;
use GeoFixer\models\queries\HousesDatabaseQuery;
use GeoFixer\traits\TranslitTrait;
use GeoFixer\traits\LevenshteinAlgorithmTrait;

/**
 * Class GeoFixer
 *
 * @package GeoFixer\models
 */
class GeoFixer
{
    use TranslitTrait;
    use LevenshteinAlgorithmTrait;

    protected $strict = false;
    protected $first_letters = false;
    protected $full_settlements = false;

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

        $word = $this->wordTranslit($word);

        $translited_words = $this->arrayTranslit($search_array);

        if ($this->strict == true) {
            $result = $this->findBestMatch($word, $translited_words);
            return $result;
        }

        $result = key($this->findMostSimilarWords($word, $translited_words));

        return $result ? $result : false;
    }

    /**
     * @param $region
     *
     * @return bool|mixed
     */
    public function findFiasRegion($region)
    {
        $regions = new RegionsDatabaseQuery();
        $regions = $regions->getRegions();

        if (is_integer($this->first_letters)) {
            $regions = $regions->firstLetters(substr($region, 0, $this->first_letters));
        }

        $regions = $regions->findAll();

        $titles = [];
        $regions_with_codes = [];

        foreach ($regions as $v) {
            $regions_with_codes[$v['title']] = $v['code'];
            $titles[] = $v['title'];
        }

        $result = $this->findSimilarWord($region, $titles);

        return $result ? $regions_with_codes[$result] : false;
    }

    /**
     * @param $city
     * @param $region_code
     *
     * @return bool|mixed
     */
    public function findFiasSettlements($city, $region_code)
    {
        $settlements = new SettlementsDatabaseQuery();
        $settlements = $settlements->getSettlements()->regionCode($region_code)->addressLevel();

        if (is_integer($this->first_letters)) {
            $settlements = $settlements->firstLetters(substr($city, 0, $this->first_letters));
        }

        $settlements = $settlements->findAll();

        $titles = [];
        $settlements_with_id = [];

        foreach ($settlements as $v) {
            $settlements_with_id[$v['title']] = $v['address_id'];
            $titles[] = $v['title'];
        }

        $result = $this->findSimilarWord($city, $titles);

        return $result ? $settlements_with_id[$result] : false;
    }

    /**
     * @param $city
     * @param $region_code
     *
     * @return bool|mixed
     */
    public function findKladrSettlements($city, $region_code)
    {
        $settlements = new SettlementsDatabaseQuery();
        $settlements = $settlements->getSettlements()->regionCode($region_code)->addressLevel();

        if (is_integer($this->first_letters)) {
            $settlements = $settlements->firstLetters(substr($city, 0, $this->first_letters));
        }

        $settlements = $settlements->findAll();

        $titles = [];
        $settlements_with_id = [];

        foreach ($settlements as $v) {
            $settlements_with_id[$v['title']] = $v['code'];
            $titles[] = $v['title'];
        }

        $result = $this->findSimilarWord($city, $titles);

        if (!$result) {
            return false;
        }
        if (is_null($settlements_with_id[$result])) {
            return false;
        }

        return $settlements_with_id[$result];
    }

    /**
     * @param $street
     * @param $city_id
     *
     * @return bool|mixed
     */
    public function findFiasStreets($street, $city_id)
    {
        $streets = new StreetsDatabaseQuery();
        $streets = $streets->getStreets()->parentId($city_id)->addressLevel();

        if (is_integer($this->first_letters)) {
            $streets = $streets->firstLetters(substr($street, 0, $this->first_letters));
        }

        $streets = $streets->findAll();

        $titles = [];
        $streets_with_id = [];

        foreach ($streets as $v) {
            $streets_with_id[$v['title']] = $v['address_id'];
            $titles[] = $v['title'];
        }

        $result = $this->findSimilarWord($street, $titles);

        return $result ? $streets_with_id[$result] : false;
    }

    /**
     * @param $street
     * @param $city_id
     *
     * @return bool|mixed
     */
    public function findKladrStreets($street, $city_id)
    {
        $streets = new StreetsDatabaseQuery();
        $streets = $streets->getStreets()->parentId($city_id)->addressLevel();

        if (is_integer($this->first_letters)) {
            $streets = $streets->firstLetters(substr($street, 0, $this->first_letters));
        }

        $streets = $streets->findAll();

        $titles = [];
        $streets_with_id = [];

        foreach ($streets as $v) {
            $streets_with_id[$v['title']] = $v['code'];
            $titles[] = $v['title'];
        }

        $result = $this->findSimilarWord($street, $titles);

        if (!$result) {
            return false;
        }
        if (is_null($streets_with_id[$result]['code'])) {
            return false;
        }

        return $streets_with_id[$result];
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
}
