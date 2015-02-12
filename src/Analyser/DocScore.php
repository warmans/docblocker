<?php
namespace Docblocker\Analyser;

/**
 * Class DocScore
 *
 * @package Docblocker\Analyser
 */
class DocScore extends AbstractAnalyser
{
    const MAX_POSSIBLE_SCORE = 10;

    /**
     * Analyse data
     */
    public function analyse()
    {
        $numElements = 0;
        foreach ($this->rawData['entities'] as &$items) {
            foreach ($items as &$item) {

                //score methods
                foreach ($item['methods'] as &$method) {
                    $method['score'] = $this->scoreMethod($method);
                }

                //score class
                $numElements++;
                $item['score'] = $this->scoreClass($item);
            }
        }
    }

    /**
     * @param $data
     * @return array
     */
    protected function scoreClass($data)
    {
        $score  = 0;
        $hints = array();

        $scoreableElements = 0;
        $scoreableElements += count($data['methods']);
        $scoreableElements += 1; //short or long description

        $elementScore = static::MAX_POSSIBLE_SCORE / $scoreableElements;

        if ($data['doc']['short_description'] || $data['doc']['long_description']) {
            $score += $elementScore;
        } else {
            $hints[] = 'Add class description';
        }

        $incompleteMethods = 0;
        foreach ($data['methods'] as $method) {
            //max possible score multiplied by decimal version of method score e.g. 0.5
            $score += $elementScore * ($method['score']['score'] / static::MAX_POSSIBLE_SCORE);
            if ($method['score']['score'] != 10) {
                 $incompleteMethods++;
            }
        }

        if ($incompleteMethods > 0) {
            $hints[] = "Improve method docs";
        }

        return array('score' => round($score, 2), 'hints' => $hints);
    }

    /**
     * @param $data
     * @return array
     */
    protected function scoreMethod($data)
    {
        //nothing to score
        if (!$data['doc']['exists']) {
            return array('score'=>0, 'hints'=>array('Add method docblock'));
        }

        $hints = array();
        $score = 0;

        $scoreableElements = 0;
        $scoreableElements += 1; //short or long desc
        $scoreableElements += 1; //return statement
        $scoreableElements += count($data['args']); //args

        $elementScore = static::MAX_POSSIBLE_SCORE / $scoreableElements;

        //score description
        if ($data['doc']['short_description'] || $data['doc']['long_description']) {
            $score += $elementScore;
        } else {
            $hints[] = 'Add a method description';
        }

        //score return tag
        $documented_returns = 0;
        foreach ($data['doc']['tags'] as $tag) {
            if (($tag['tag'] === 'return') && $tag['value']) {
                $documented_returns++;
                break;
            }
        }

        switch (true) {
            case ($documented_returns === 1):
                $score += $elementScore;
                break;
            case ($documented_returns > 1):
                $hints[] = 'Too many return tags';
                break;
            case ($documented_returns < 1):
                $hints[] = 'Add a return tag';
                break;
        }

        //score param docs
        if (count($data['args'])) {
            $documented_args = 0;
            foreach ($data['doc']['tags'] as $tag) {
                if (($tag['tag'] === 'param') && $tag['value']) {
                    $documented_args++;
                }
            }

            switch (true) {
                case ($documented_args === count($data['args'])):
                    $score += ($documented_args*$elementScore);
                    break;
                case ($documented_args > count($data['args'])):
                    $hints[] = 'Too many param tags. Method has '.count($data['args']).' arguments but has '.$documented_args.' param tags';
                    break;
                case ($documented_returns < count($data['args'])):
                    $hints[] = 'Not enough param tags. Method has '.count($data['args']).' arguments but has '.$documented_args.' param tags';
                    break;
            }
        }

        return array('score' => round($score, 2), 'hints' => $hints);
    }
}
