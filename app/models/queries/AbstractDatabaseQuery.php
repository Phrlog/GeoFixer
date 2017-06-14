<?php

namespace GeoFixer\models\queries;

use Cake\Database\Connection;
use GeoFixer\models\DatabaseConnection;

/**
 * Class AbstractDatabaseQuery
 *
 * @package GeoFixer\models\queries
 */
class AbstractDatabaseQuery
{
    /**
     * Address levels в базе ФИАС
     */
    const CITY = 4;
    const SETTLEMENT = 6;
    const STREET = 7;

    /**
     * @var Connection::newQuery()
     */
    protected $db;

    /**
     * AbstractDatabaseQuery constructor.
     *
     * @uses Connection::newQuery()
     */
    public function __construct()
    {
        $this->db = DatabaseConnection::connection()->newQuery();
    }

    /**
     * Первые буквы равны
     *
     * @param $letters
     *
     * @return $this
     */
    public function firstLetters($letters)
    {
        $this->db = $this->db->andWhere(['title LIKE' => $letters . '%']);

        return $this;
    }

    /**
     * Код региона равен
     *
     * @param $code
     *
     * @return $this
     */
    public function regionCode($code)
    {
        $this->db = $this->db->andWhere(['region_code' => $code]);

        return $this;
    }

    /**
     * Код КЛАДР равен
     *
     * @param $code
     *
     * @return $this
     */
    public function kladrCode($code)
    {
        $this->db = $this->db->andWhere(['code' => $code]);

        return $this;
    }

    /**
     * ID родителя равен
     *
     * @param $id
     *
     * @return $this
     */
    public function parentId($id)
    {
        $this->db = $this->db->andWhere(['parent_id' => $id]);

        return $this;
    }

    /**
     * Получаем массив значений
     *
     * @return array
     */
    public function findAll()
    {
        return $this->db->execute()->fetchAll('assoc');
    }

    /**
     * Получаем одно значение
     *
     * @return array
     */
    public function findOne()
    {
        return $this->db->execute()->fetch('assoc');
    }
}