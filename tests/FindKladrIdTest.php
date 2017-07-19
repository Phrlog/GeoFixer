<?php

namespace GeoFixer\tests;
use GeoFixer\GeoFixerFacade;

/**
 * Class FindFiasIdTest
 *
 * @group fias
 * @package GeoFixer\tests
 */
class FindKladrIdTest extends \PHPUnit_Framework_TestCase
{
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->facade = new GeoFixerFacade($fias = true);
    }

    public function testFindKladrRegion()
    {
        $region = 'Ленинрадская область';

        $this->assertEquals(4700000000000, $this->facade->findKladrRegion($region));
    }

    public function testFindStrictKladrRegion()
    {
        $region = 'Ленинрадская область';

        $this->assertEquals(4700000000000, $this->facade->findKladrRegion($region, 2, true));
    }

    public function testRegionError()
    {
        $region = 'Банановая респубика';

        $this->assertFalse($this->facade->findKladrRegion($region, false, true));
    }

    public function testFindKladrSettlement()
    {
        $city = 'Блоговщнск'; // Благовещенск
        $region_code = 2800000000000;

        $this->assertEquals('2800000100000', $this->facade->findKladrSettlement($city, $region_code));
    }

    public function testFindStrictKladrSettlement()
    {
        $city = 'Блоговщнск'; // Благовещенск
        $region_code = 2800000000000;

        $this->assertEquals('2800000100000', $this->facade->findKladrSettlement($city, $region_code, 2));
    }

    public function testSettlementError()
    {
        $city = 'Эльдорадо';
        $region = 2800000000000;

        $this->assertFalse($this->facade->findKladrSettlement($city, $region, false, true));
    }

    public function testFindRegionAndCity()
    {
        $region = 'Чеченская республика';
        $city = 'Грозный';

        $this->assertEquals('2000000100000', $this->facade->findKladrSettlement($city, $this->facade->findKladrRegion($region)));
    }

    public function testFindStreet()
    {
        $city_code = '2800000100000';
        $street = 'Амурская';

        $this->assertEquals('28000001000000200', $this->facade->findKladrStreet($street, $city_code));
    }

    public function testFindStrictStreet()
    {
        $city_code = '2800000100000';
        $street = 'Амурская';

        $this->assertEquals('28000001000000200', $this->facade->findKladrStreet($street, $city_code, 2));
    }

    public function testStreetError()
    {
        $city_code = '2800000100000';
        $street = 'Несуществующая';

        $this->assertFalse($this->facade->findKladrStreet($street, $city_code, false, $strict_search = true));
    }

    public function testCityError()
    {
        $city_code = '28000000';
        $street = 'Несуществующая';

        $this->assertFalse($this->facade->findKladrStreet($street, $city_code, false, $strict_search = true));
    }

}