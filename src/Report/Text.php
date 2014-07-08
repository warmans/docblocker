<?php
namespace Docblocker\Report;

use Docblocker\Analyser\DocScore;

class Text
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function render()
    {
        ob_start();

        $overview = $this->data['overview'];

        echo "\n\nSummary\n";
        echo str_repeat('-', 80)."\n";
        echo "Project Score: ".$overview['score']."\n";
        echo "Class Doc Coverage: ".($overview['classes_with_docs']/$overview['classes_total']*100)."%\n";
        echo "Method Doc Coverage: ".($overview['methods_with_docs']/$overview['methods_total']*100)."%\n";
        echo "Interface Doc Coverage: ".($overview['interfaces_with_docs']/$overview['interfaces_total']*100)."%\n";

        echo "\nIssues\n";
        echo str_repeat('-', 80)."\n";
        foreach ($this->data['entities'] as $entityName => $entityGroup) {
            foreach ($entityGroup as $entity) {
                if ($entity['score']['score'] < 10) {
                    foreach ($entity['methods'] as $method) {
                        if ($method['score']['score'] < 10) {
                            echo "{$entity['name']}::{$method['name']} scored {$method['score']['score']} out of ".DocScore::MAX_POSSIBLE_SCORE."\n";
                            foreach ($method['score']['hints'] as $hint) {
                                echo "* $hint\n";
                            }
                            echo "\n";
                        }
                    }
                }

            }
        }

        return ob_get_clean();
    }
}
