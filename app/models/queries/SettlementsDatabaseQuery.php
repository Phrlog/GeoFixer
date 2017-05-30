<?php

namespace GeoFixer\models\queries;

/**
 * Class SettlementsDatabaseQuery
 *
 * @package GeoFixer\models\queries
 */
class SettlementsDatabaseQuery extends AbstractDatabaseQuery
{
    /**
     * SettlementsDatabaseQuery constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Получаем города и поселения
     *
     * @return $this
     */
    public function getSettlements()
    {
        $this->db = $this->db->select(['address_id', 'title'])->from('fias_address_object');

        return $this;
    }

    /**
     * Address level равен городу или поселению
     *
     * @param bool $full_settlements
     *
     * @return $this
     */
    public function addressLevel($full_settlements = false)
    {
        if ($full_settlements == false) {
            $this->db = $this->db->andWhere(['address_level' => self::CITY]);
        } else {
            $this->db = $this->db->andWhere(['OR' => [['address_level' => self::CITY], ['address_level' => self::SETTLEMENT]]]);
        }

        return $this;
    }
}