<?php

namespace GeoFixer\models\queries;

/**
 * Class StreetsDatabaseQuery
 *
 * @package GeoFixer\models\queries
 */
class StreetsDatabaseQuery extends AbstractDatabaseQuery
{
    /**
     * RegionsDatabaseQuery constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Получаем улицы
     *
     * @return $this
     */
    public function getStreets()
    {
        $this->db = $this->db->select(['address_id', 'title', 'code'])->from('fias_address_object');

        return $this;
    }

    /**
     * Address level равен улице
     *
     * @return $this
     */
    public function addressLevel()
    {
        $this->db = $this->db->andWhere(['address_level' => self::STREET]);

        return $this;
    }

}
