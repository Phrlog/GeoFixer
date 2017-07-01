<?php

namespace GeoFixer\helpers;

/**
 * Class FuzzySearchHelper
 *
 * @package GeoFixer\helpers
 */
class FuzzySearchHelper
{

    public $similarity = 0;
    public $meta_similarity = 0;
    public $min_levenshtein = 1000;
    public $meta_min_levenshtein = 1000;

    /**
     * Строгий поиск
     *
     * @param $word
     * @param $translited_words
     *
     * @return bool|mixed
     */
    public function findBestMatch($word, $translited_words)
    {
        $words_array = [];
        foreach ($translited_words as $russian => $translit) {
            if (levenshtein(metaphone($word), metaphone($translit)) < mb_strlen(metaphone($word)) / 2) {
                if (levenshtein($word, $translit) < mb_strlen($word) / 2) {
                    $words_array[$russian] = $translit;
                }
            }
        }

        $most_similar = $this->findMostSimilarWords($word, $words_array);
        $this->findFinalSimilirityAndLevenshtein($most_similar, $word);

        foreach ($most_similar as $russian => $latin) {
            if (levenshtein(metaphone($latin), metaphone($word)) <= $this->meta_min_levenshtein) {
                if (similar_text(metaphone($latin), metaphone($word)) >= $this->meta_similarity) {
                    $meta_result[$russian] = $latin;
                }
            }
        }

        return @key($meta_result) ? @key($meta_result) : false;
    }

    /**
     * Нестрогий поиск
     *
     * @param $word
     * @param $words_array
     * @return array
     */
    public function findMostSimilarWords($word, $words_array)
    {
        $this->min_levenshtein = 1000;
        $this->similarity = 0;

        $result = [];

        foreach ($words_array as $russian => $translit) {
            if (levenshtein($translit, $word) <= $this->min_levenshtein) {
                if (similar_text($translit, $word) >= $this->similarity) {
                    $this->min_levenshtein = levenshtein($translit, $word);
                    $this->similarity = similar_text($translit, $word);
                    $result = [];
                    $result[$russian] = $translit;
                }
            }
        }

        return $result;
    }

    /**
     * Вспомогательный метод для findBestMatch
     *
     * @param $most_similar
     * @param $word
     */
    private function findFinalSimilirityAndLevenshtein($most_similar, $word)
    {
        foreach ($most_similar as $item) {
            $this->meta_min_levenshtein = min($this->meta_min_levenshtein, levenshtein(metaphone($item), metaphone($word)));
        }

        foreach ($most_similar as $item) {
            if (levenshtein($item, $word) == $this->meta_min_levenshtein) {
                $this->meta_similarity = max($this->meta_similarity, similar_text(metaphone($item), metaphone($word)));
            }
        }
    }

}