<?php

namespace GeoFixer\tests;

use GeoFixer\GeoFixerFacade;
use GeoFixer\models\DatabaseConnection;
use GeoFixer\models\queries\HousesDatabaseQuery;
use GeoFixer\models\queries\RegionsDatabaseQuery;
use GeoFixer\models\queries\SettlementsDatabaseQuery;
use GeoFixer\models\queries\StreetsDatabaseQuery;

/**
 * Class ModelQueriesTest
 *
 * @group fias
 * @package GeoFixer\tests
 */
class ModelQueriesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GeoFixerFacade
     */
    protected $facade;

    public function testFiasFacadeErrorCreation()
    {
        $this->setExpectedException(null, 'Database connection error');

        $config = [
            'host' => 'localhost',
            'database'  => 'fias',
            'username'  => 'root',
            'password'  => 'wrong_password'
        ];
        $this->facade = new GeoFixerFacade($fias = true, $config);
    }

    public function testFiasFacadeCreation()
    {
        $this->facade = new GeoFixerFacade($fias = true);

        $this->assertNotEmpty($this->facade);
    }

    public function testConnection()
    {
        $connection = new DatabaseConnection();

        $this->assertNotEmpty($connection->connection());
    }

    public function testGetRegions()
    {
        $regions = new RegionsDatabaseQuery();
        $regions = $regions->getRegions()->findAll();

        $this->assertNotEmpty($regions);
    }

    public function testGetSettlements()
    {
        $settlements = new SettlementsDatabaseQuery();
        $settlements = $settlements->getSettlements()->regionCode(28)->addressLevel()->findAll();

        $this->assertNotEmpty($settlements);
    }

    public function testGetAllSettlements()
    {
        $settlements = new SettlementsDatabaseQuery();
        $settlements = $settlements->getSettlements()->regionCode(28)->addressLevel($full_settlements = true)->findAll();

        $this->assertNotEmpty($settlements);
    }

    public function testGetStreets()
    {
        $streets = new StreetsDatabaseQuery();
        $streets = $streets->getStreets()->parentId('8f41253d-6e3b-48a9-842a-25ba894bd093')->addressLevel()->findAll();

        $this->assertNotEmpty($streets);
    }

    public function testGetHouses()
    {
        $houses = new HousesDatabaseQuery();
        $houses = $houses->getHouses()->addressId('3e0d1213-1212-4f87-bdd3-5f8ef6f6473e')->findAll();

        $this->assertNotEmpty($houses);
    }

    public function testGetHouseWithBuilding()
    {
        $house_id = new HousesDatabaseQuery();
        $house_id = $house_id->getHouses()->addressId('17c1f81e-e29a-4708-945b-f0325f97360e')->houseNumber('116')->building('Ю')->findOne();

        $this->assertEquals('2d5410f7-d663-4989-b185-6545a1601297', $house_id['house_id']);
    }
}
