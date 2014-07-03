<?php
namespace Docblocker;


class Analyzer
{
    protected $rawData;

    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    public function getAnalysis()
    {
        return array_merge(
            $this->getOverview(),
            $this->getScores()
        );
    }

    protected function getOverview()
    {
        $results = array('overview' => array());

        $methods_total = 0;
        $methods_with_docs = 0;

        foreach($this->rawData as $groupName => $items) {

            $structure_with_docs = 0;

            foreach ($items as $item) {
                if ($item['doc']['exists']) {
                    $structure_with_docs++;
                }
                foreach ($item['methods'] as $method) {
                    $methods_total++;
                    if ($method['doc']['exists']) {
                        $methods_with_docs++;
                    }
                }
            }

            $results['overview'] = array_merge(
                $results['overview'],
                array(
                    $groupName.'_with_docs' => $structure_with_docs,
                    $groupName.'_total' => count($items),
                    'methods_with_docs' => $methods_with_docs,
                    'methods_total' => $methods_total
                )
            );
        }

        return $results;
    }

    protected function getScores()
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