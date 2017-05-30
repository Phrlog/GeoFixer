<?php

namespace GeoFixer\tests;
use GeoFixer\GeoFixerFacade;

/**
 * Class FindFiasIdTest
 *
 * @group fias
 * @package GeoFixer\tests
 */
class FindFiasIdTest extends \PHPUnit_Framework_TestCase
{
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->facade = new GeoFixerFacade($fias = true);
    }

    public function testFindFiasRegion()
    {
        $region = 'Ленинрадская область';

        $this->assertEquals(47, $this->facade->findFiasRegion($region));
    }

    public function testFindFiasSettlement()
    {
        $city = 'Блоговщнск'; // Благовещенск
        $region_code = 28;

        $this->assertEquals('8f41253d-6e3b-48a9-842a-25ba894bd093', $this->facade->findFiasSettlement($city, $region_code));
    }

    public function testFindRegionAndCity()
    {
        $region = 'Чеченская республика';
        $city = 'Грозный';

        $this->assertEquals('a2072dc5-45be-4db3-ab13-10784ba8b2ae', $this->facade->findFiasSettlement($city, $this->facade->findFiasRegion($region)));
    }

    public function testFindStreet()
    {
        $city_id = '8f41253d-6e3b-48a9-842a-25ba894bd093';
        $street = 'Амурская';

        $this->assertEquals('3e0d1213-1212-4f87-bdd3-5f8ef6f6473e', $this->facade->findFiasStreet($street, $city_id));
    }

    public function testFindHouse()
    {
        $street_id = '3e0d1213-1212-4f87-bdd3-5f8ef6f6473e';
        $house = 261;

        $this->assertEquals('05f9a72c-da04-41a1-908c-140e7ed3b29f', $this->facade->findFiasHouse($house, $street_id));
    }

    public function testFindHouseWithBuilding()
    {
        $street_id = '17c1f81e-e29a-4708-945b-f0325f97360e';
        $house = 116;
        $building = 'Ю';

        $this->assertEquals('2d5410f7-d663-4989-b185-6545a1601297', $this->facade->findFiasHouse($house, $street_id, $building));
    }

    public function testRegionError()
    {
        $region = 'Банановая респубика';

        $this->assertFalse($this->facade->findFiasRegion($region, false, true));
    }

    public function testCityError()
    {
        $city = 'Эльдорадо';
        $region = 28;

        $this->assertFalse($this->facade->findFiasSettlement($city, $region, false, true));
    }

    public function testFindHouseWithBuildingError()
    {
        $street_id = '17c1f81e-e29a-4708-945b-f0325f97360e';
        $house = 116;
        $building = '23';

        $this->assertFalse($this->facade->findFiasHouse($house, $street_id, $building));
    }
}