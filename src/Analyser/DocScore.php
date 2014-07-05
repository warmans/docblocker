<?php
namespace Docblocker\Analyser;

class DocScore extends AbstractAnalyser
{
    public function analyse()
    {
        $results = array('scores' => array());
        foreach ($this->rawData as $groupName => $items) {
            $results['scores'][$groupName] = array();
            foreach ($items as $item) {
                $score = array(
                    'score' => 0,
                    'name' => $item['name'],
                    'methods' => array()
                );
                foreach ($item['methods'] as $method) {
                    $score['methods'][] = array('score' => $this->scoreDoc($method['doc']), 'name'=>$method['name']);
                }
                $results['scores'][$groupName][] = $score;
            }
        }
        return $results;
    }

    protected function scoreDoc($doc)
    {
        if (!$doc['exists']) {
            return 0; //do not pass go
        }

        $score = 10;
        if (!$doc['short_description'] && $doc['long_description']) {
            $score = $score-5;
        }
        /**
         * @todo needs information in the raw data about how many params were specified
         */
        if (count($doc['tags']) == 0) {
            $score = $score-5;
        }

        return $score;
    }
}
