<?php
namespace Docblocker\Analyser;

class ProjectScore extends AbstractAnalyser
{
    const MAX_POSSIBLE_SCORE = 10;

    public function analyse()
    {
        $this->rawData['project'] = array();

        $numElements = 0;
        foreach ($this->rawData['entities'] as $items) {
            $numElements += count($items);
        }

        //score available per class/interface
        $elementScore = static::MAX_POSSIBLE_SCORE / $numElements;

        $projectScore = 0;
        foreach ($this->rawData['entities'] as $scoredItems) {
            foreach ($scoredItems as $scoredItem) {
                $projectScore += $elementScore * ($scoredItem['score']['score'] / static::MAX_POSSIBLE_SCORE);
            }
        }

        $this->rawData['project']['score'] = round($projectScore, 2);
    }
}
