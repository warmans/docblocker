<?php
namespace Docblocker\Analyser;

class DocScore extends AbstractAnalyser
{
    const MAX_POSSIBLE_SCORE = 10;

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

        //score available per class/interface
        $elementScore = static::MAX_POSSIBLE_SCORE / $numElements;

        $projectScore = 0;
        foreach ($this->rawData['entities'] as $scoredItems) {
            foreach ($scoredItems as $scoredItem) {
                $projectScore += $elementScore * ($scoredItem['score']['score'] / static::MAX_POSSIBLE_SCORE);
            }
        }

        $this->rawData['overview']['score'] = round($projectScore, 2);
    }

    protected function scoreClass($data)
    {
        if (!$data['doc']['exists']) {
            return array('score'=>0, 'hints'=>array('Add class docblock'));
        }

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
            $score += $elementScore * ($method['score']['score'] / static::MAX_POSSIBLE_SCORE); //max possible score multiplied by decimal version of method score e.g. 0.5
            if ($method['score']['score'] != 10) {
                 $incompleteMethods++;
            }
        }

        if ($incompleteMethods > 0) {
            $hints[] = "Improve method docs";
        }

        return array('score' => round($score, 2), 'hints' => $hints);
    }

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
