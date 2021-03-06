<?php
namespace Docblocker;

use Docblocker\Console\ProgressAwareInterface;
use SplObserver;

class Analyser implements \SplSubject, ProgressAwareInterface
{
    /**
     * @var AbstractAnalyser[]
     */
    protected $analysers;

    /**
     * @var \SplObjectStorage
     */
    protected $observers;

    /**
     * @var int
     */
    protected $analysersRun = 0;


    /**
     * @param array $analysers
     */
    public function __construct(array $analysers)
    {
        $this->analysers = $analysers;

        $this->observers = new \SplObjectStorage();
    }

    /**
     * Run all configured analysers
     */
    public function runAll()
    {
        $this->analysersRun = 0;
        foreach ($this->analysers as $analyser) {
            $analyser->analyse();
            $this->analysersRun++;
            $this->notify();
        }
    }

    /**
     * @return int
     */
    public function getProgress()
    {
        return $this->analysersRun;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return ($this->analysersRun >= count($this->analysers));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Attach an SplObserver
     * @link http://php.net/manual/en/splsubject.attach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to attach.
     * </p>
     * @return void
     */
    public function attach(SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Detach an observer
     * @link http://php.net/manual/en/splsubject.detach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to detach.
     * </p>
     * @return void
     */
    public function detach(SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Notify an observer
     * @link http://php.net/manual/en/splsubject.notify.php
     * @return void
     */
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}
