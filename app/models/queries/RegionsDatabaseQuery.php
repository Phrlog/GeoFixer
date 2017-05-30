<?php

namespace GeoFixer\models\queries;

/**
 * Class RegionsDatabaseQuery
 *
 * @package GeoFixer\models\queries
 */
class RegionsDatabaseQuery extends AbstractDatabaseQuery
{
    /**
     * RegionsDatabaseQuery constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Получаем регионы
     *
     * @return $this
     */
    public function getRegions()
    {
        $this->db = $this->db->select('*')->from('fias_region');

        return $this;
    }
}