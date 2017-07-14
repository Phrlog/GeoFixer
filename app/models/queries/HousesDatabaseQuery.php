<?php

namespace GeoFixer\models\queries;

/**
 * Class HousesDatabaseQuery
 *
 * @package GeoFixer\models\queries
 */
class HousesDatabaseQuery extends AbstractDatabaseQuery
{
    /**
     * RegionsDatabaseQuery constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Получаем дома
     *
     * @return $this
     */
    public function getHouses()
    {
        $this->db = $this->db->select(['house_id', 'number'])->from('fias_house');

        return $this;
    }

    /**
     * Address ID равен
     *
     * @param $street_id
     *
     * @return $this
     */
    public function addressId($street_id)
    {
        $this->db = $this->db->andWhere([self::FIAS_CODE  => $street_id]);

        return $this;
    }

    /**
     * Номер дома равен
     *
     * @param $number
     *
     * @return $this
     */
    public function houseNumber($number)
    {
        $this->db = $this->db->andWhere(['number' => $number]);

        return $this;
    }

    /**
     * Номер корпуса равен
     *
     * @param $building
     *
     * @return $this
     */
    public function building($building)
    {
        $this->db = $this->db->andWhere(['building' => $building]);

        return $this;
    }
}
