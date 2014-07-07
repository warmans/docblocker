<?php
namespace Docblocker\Analyser;

class Overview extends AbstractAnalyser
{
    public function analyse()
    {
        $this->rawData['overview'] = array();

        $methods_total = 0;
        $methods_with_docs = 0;

        foreach ($this->rawData['entities'] as $groupName => $items) {

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

            $this->rawData['overview'] = array_merge(
                $this->rawData['overview'],
                array(
                    $groupName.'_with_docs' => $structure_with_docs,
                    $groupName.'_total' => count($items),
                    'methods_with_docs' => $methods_with_docs,
                    'methods_total' => $methods_total
                )
            );
        }
    }
}
