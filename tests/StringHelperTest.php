<?php

namespace GeoFixer\tests;
use GeoFixer\helpers\StringHelper;

/**
 * Class TranslitTraitTest
 *
 * @group common
 * @package GeoFixer\tests
 */
class TranslitTraitTest extends \PHPUnit_Framework_TestCase
{
     public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->string_helper = new StringHelper();
    }

    public function testRemoveSymbols()
    {
        $word = 'xxxТе1кст%mk456';

        $result = $this->string_helper->removeSymbols($word);

        $this->assertEquals('xxxТекстmk', $result);
    }

    public function testRemoveSpecifications()
    {
        $words = [
            'край Прекрасный' => 'Прекрасный', 'автономная область Воркута' => 'Воркута', 'автономный округ Мирный' => 'Мирный', 'ао Давно' => 'Давно',
            'республика Камыш' => 'Камыш', 'респ. Публика' => 'Публика', 'дер. Мармеладок' => 'Мармеладок', 'деревня Докучаево' => 'Докучаево', 'д. Домашний' => 'Домашний',
            'г. Городище' => 'Городище'
        ];

        foreach ($words as $word => $result) {
            $this->assertEquals($result, $this->string_helper->removeSpecifications($word));
        }
    }

    public function testWordTranslit()
    {
        $words = [
            'край Прекрасный' => 'prekrasnyiy', 'автономная область Воркута' => 'vorkuta', 'автономный округ Мирный' => 'mirnyiy', 'ао Давно' => 'davno',
            'республика Камыш' => 'kamyish', 'Респ. Публика' => 'publika', 'дер. Мармеладок' => 'marmeladok', 'деревня Докучаево' => 'dokuchaevo', 'д. Домашний' => 'domashniy',
            'г. Городище' => 'gorodische'
        ];

        foreach ($words as $word => $result) {
            $this->assertEquals($result, $this->string_helper->wordTranslit($word));
        }

    }
}
