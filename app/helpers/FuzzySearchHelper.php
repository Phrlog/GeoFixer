<?php

namespace GeoFixer\helpers;

/**
 * Class FuzzySearchHelper
 *
 * @package GeoFixer\helpers
 */
class FuzzySearchHelper
{
    public $max_similarity;
    public $meta_similarity;
    public $min_levenshtein;
    public $meta_min_levenshtein;

    public $result_array = [];
    public $words_array = [];

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
        $this->refreshVariables();

        array_walk($translited_words, array($this, 'strictWordsLevenshteinHandlerCallback'), $word);
        $most_similar = $this->findMostSimilarWords($word, $this->words_array);
        array_walk($most_similar, array($this, 'findFinalSimilirityAndLevenshtein'), $word);
        array_walk($most_similar, array($this, 'strictSimilarityCallback'), $word);

        if (is_null(key($this->result_array))) {
            return false;
        }

        return key($this->result_array);
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
        $this->refreshVariables();

        array_walk($words_array, array($this, 'similarityCallback'), $word);

        return $this->result_array;
    }

    /**
     * Вспомогательный метод для findBestMatch
     *
     * @param $item
     * @param $key
     * @param $word
     */
    private function findFinalSimilirityAndLevenshtein($item, $key, $word)
    {
        $this->meta_min_levenshtein = min($this->meta_min_levenshtein, levenshtein(metaphone($item), metaphone($word)));
        if (levenshtein($item, $word) == $this->meta_min_levenshtein) {
            $this->meta_similarity = max($this->meta_similarity, similar_text(metaphone($item), metaphone($word)));
        }
    }

    /**
     * Вспомогательный метод для findBestMatch
     *
     * @param $latin
     * @param $russian
     * @param $word
     */
    private function strictSimilarityCallback($latin, $russian, $word)
    {
        if (levenshtein(metaphone($latin), metaphone($word)) <= $this->meta_min_levenshtein) {
            if (similar_text(metaphone($latin), metaphone($word)) >= $this->meta_similarity) {
                $this->result_array[$russian] = $latin;
            }
        }
    }

    /**
     * Вспомогательный метод для findBestMatch
     *
     * @param $translit
     * @param $russian
     * @param $word
     */
    private function strictWordsLevenshteinHandlerCallback($translit, $russian, $word)
    {
        if (levenshtein(metaphone($word), metaphone($translit)) < mb_strlen(metaphone($word)) / 2) {
            if (levenshtein($word, $translit) < mb_strlen($word) / 2) {
                $this->words_array[$russian] = $translit;
            }
        }
    }

    /**
     * Вспомогательный метод для findMostSimilarWords
     *
     * @param $translit
     * @param $russian
     * @param $word
     */
    private function similarityCallback($translit, $russian, $word)
    {
        if (levenshtein($translit, $word) <= $this->min_levenshtein) {
            if (similar_text($translit, $word) >= $this->max_similarity) {
                $this->result_array = [];
                $this->min_levenshtein = levenshtein($translit, $word);
                $this->max_similarity = similar_text($translit, $word);
                $this->result_array[$russian] = $translit;
            }
        }
    }

    /**
     * Refresh variables
     */
    private function refreshVariables()
    {
        $this->max_similarity = 0;
        $this->meta_similarity = 0;
        $this->min_levenshtein = 1000;
        $this->meta_min_levenshtein = 1000;
    }

}
