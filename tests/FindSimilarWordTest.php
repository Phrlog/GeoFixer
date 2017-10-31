<?php

namespace GeoFixer\tests;

use GeoFixer\GeoFixerFacade;

/**
 * Class FindSimilarWordTest
 *
 * @group common
 * @package GeoFixer\tests
 */
class FindSimilarWordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GeoFixerFacade
     */
    protected $facade;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->facade = new GeoFixerFacade();
    }

    public function testFacadeCreation()
    {
        $this->facade = new GeoFixerFacade();

        $this->assertNotEmpty($this->facade);
    }

    public function testFacadeCreationWithLoggerEnable()
    {
        $this->facade = new GeoFixerFacade(false, true);

        $this->assertNotEmpty($this->facade);
    }

    public function testFindSimilarRegion()
    {
        $regions = [
            'Ханты-Мансийский автономный округ', 'Краснодарский край', 'Москвовская область', 'Башкирия', 'Ленинрадская область', 'Удмуртская республика'
        ];

        $this->assertEquals('Ханты-Мансийский автономный округ', $this->facade->findSimilarWord('Ханты-Мансийский автономный округ - Югра', $regions));
        $this->assertEquals('Ханты-Мансийский автономный округ', $this->facade->findSimilarWord('Ханты-Мансийский АО', $regions));
        $this->assertEquals('Ханты-Мансийский автономный округ', $this->facade->findSimilarWord('Ханты-Мансийский', $regions));

        $this->assertEquals('Башкирия', $this->facade->findSimilarWord('Башкортостан', $regions));
        $this->assertEquals('Ленинрадская область', $this->facade->findSimilarWord('Лен. область', $regions));
        $this->assertEquals('Удмуртская республика', $this->facade->findSimilarWord('Удмуртия', $regions));
    }

    public function testFindSimilarCity()
    {
        $cities = [
            'Москва', 'Тольятти', 'Пермь', 'Екатеринбург', 'Московия'
        ];

        $this->assertEquals('Москва', $this->facade->findSimilarWord('Москва', $cities));
        $this->assertEquals('Москва', $this->facade->findSimilarWord('город Москва', $cities));
        $this->assertEquals('Москва', $this->facade->findSimilarWord('г. Москва', $cities));
    }

    public function testFindSimilarError()
    {
        $cities = [
            'Москва', 'Тольятти', 'Пермь', 'Екатеринбург', 'Московия'
        ];

        $this->assertFalse($this->facade->findSimilarWord('Нью-Йорк', $cities, $strict_search = true));
    }
}
