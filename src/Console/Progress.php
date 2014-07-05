<?php
namespace Docblocker\Console;

use SplSubject;
use Symfony\Component\Console\Helper\ProgressBar;

class Progress extends ProgressBar implements \SplObserver {

    /**
     * Receive update from subject
     *
     * @link http://php.net/manual/en/splobserver.update.php
     * @param SplSubject $subject
     * @return void
     */
    public function update(SplSubject $subject)
    {
        $this->setCurrent($subject->getProgress());

        if ($subject->isFinished()) {
            $this->finish();
        }
    }
}
