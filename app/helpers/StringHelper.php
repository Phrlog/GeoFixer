<?php

namespace GeoFixer\helpers;

/**
 * Class StringHelper
 *
 * @package GeoFixer\helpers
 */
class StringHelper {

    /**
     * Массив букв для транслитерации
     *
     * @var array
     */
    public $alphabet = [
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ё"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"Kh","Ц"=>"Ts","Ч"=>"Ch",
        "Ш"=>"Sh","Щ"=>"Sch","Ъ"=>"","Ы"=>"Yi","Ь"=>"",
        "Э"=>"E","Ю"=>"Yu","Я"=>"Ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"'","э"=>"e","ю"=>"yu","я"=>"ya",

        "Ą"=>"A",'Ć'=>'C',"Ę"=>"E",
        "Ł"=>"L","Ń"=>"N","Ó"=>"O",
        "Ś"=>"S","Ź"=>"Z","Ż"=>"Z",
        "ą"=>"a", "ę"=>"e","ł"=>"l",
        "ń"=>"n","ó"=>"o","ć"=>"c",
        "ś"=>"s","ź"=>"z","ż"=>"z",
    ];

    /**
     * Транслитерируем слово
     *
     * @param $word
     *
     * @return string
     */
    public function wordTranslit($word) {
        mb_internal_encoding("UTF-8");
        $word = mb_strtolower($word);
        $word = $this->removeSpecifications($word);
        $word = $this->removeSymbols($word);

        return strtr($word, $this->alphabet);
    }

    /**
     * Транслитерируем массив слов
     *
     * @param $array
     *
     * @return array
     */
    public function arrayTranslit($array) {
        $result = [];

        foreach ($array as $word) {
            $result[$word] = $this->wordTranslit($word);
        }

        return $result;
    }

    /**
     * Убираем пробелы и лишние символы, оставляем только кириллицу и латиницу
     *
     * @param $word
     * @return mixed
     */
    public function removeSymbols($word)
    {
        return preg_replace("/[^,\p{Cyrillic}\p{Latin}]/ui", '', $word);
    }

    /**
     * Убираем префиксы
     *
     * @param $word
     * @return mixed
     */
    public function removeSpecifications($word)
    {
        $search = include dirname(dirname(__FILE__)) . '/config/specifications.php';

        return str_ireplace($search, '', $word);
    }
}
