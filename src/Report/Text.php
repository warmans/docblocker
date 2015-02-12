<?php
namespace Docblocker\Report;

use Docblocker\Analyser\DocScore;
use Docblocker\Analyser\ProjectScore;

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

        echo "\n\nProject\n";
        echo str_repeat('-', 80)."\n";
        echo "Overall Score: ".$this->data['project']['score']." / ".ProjectScore::MAX_POSSIBLE_SCORE."\n";

        echo "\nCoverage\n";
        echo str_repeat('-', 80)."\n";
        echo "Class Coverage: ".round($overview['classes_with_docs']/$overview['classes_total']*100, 2)."%\n";
        echo "Method Coverage: ".round($overview['methods_with_docs']/$overview['methods_total']*100, 2)."%\n";
        echo "Interface Coverage: ".round($overview['interfaces_with_docs']/$overview['interfaces_total']*100, 2)."%\n";

        echo "\nIssues\n";
        echo str_repeat('-', 80)."\n";
        foreach ($this->data['entities'] as $entityName => $entityGroup) {
            foreach ($entityGroup as $entity) {
                if ($entity['score']['score'] < DocScore::MAX_POSSIBLE_SCORE) {
                    foreach ($entity['methods'] as $method) {
                        if ($method['score']['score'] < DocScore::MAX_POSSIBLE_SCORE) {
                            echo "{$entity['name']}::{$method['name']} scored {$method['score']['score']} out of ".DocScore::MAX_POSSIBLE_SCORE."\n";
                            foreach ($method['score']['hints'] as $hint) {
                                echo "- $hint\n";
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
