<?php
namespace Docblocker;

use Docblocker\Console\ProgressAwareInterface;
use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\FileReflector;
use SplObserver;

class CodeParser implements \SplSubject, ProgressAwareInterface
{
    /**
     * @var \SplObjectStorage
     */
    private $observers;

    /**
     * @var int
     */
    private $numFilesToProcess = 0;

    /**
     * @var int
     */
    private $numFilesProcessed = 0;

    public function __construct()
    {
        $this->observers = new \SplObjectStorage();
    }

    /**
     * @param array $filemap
     * @return array
     */
    public function parseFiles(array $filemap)
    {
        $this->numFilesToProcess = count($filemap);
        $this->numFilesProcessed = 0;

        $results = array();
        foreach ($filemap as $abspath => $filename) {

            //reflect class
            $file = new FileReflector($abspath);
            $file->process();

            foreach(array('classes' => $file->getClasses(), 'interfaces' => $file->getInterfaces()) as $entity_name => $entities) {

                if (!isset($results[$entity_name])) {
                    //init entity type
                    $results[$entity_name] = array();
                }

                foreach ($entities as $entity) {

                    $result = array(
                        //top level class attributes
                        'name' => $entity->getName(),
                        'namespace' => $entity->getNamespace(),
                        'doc' => $this->parseDocblock($entity),

                        //method attributes
                        'methods' => array(),
                    );

                    foreach ($entity->getMethods() as $method) {
                        $result['methods'][] = array(
                            'name' => $method->getName(),
                            'doc' => $this->parseDocblock($method)
                        );
                    }

                    //store result
                    $results[$entity_name][] = $result;
                }
            }

            $this->numFilesProcessed++;
            $this->notify();
        }

        return $results;
    }

    /**
     * @param BaseReflector $reflection
     * @return array
     */
    protected function parseDocblock (BaseReflector $reflection)
    {
        $doc = $reflection->getDocBlock();
        if ($doc) {
            return array(
                'exists' => true,
                'short_description' => (string) $doc->getShortDescription(),
                'long_description' => (string) $doc->getLongDescription(),
                'tags' => array_map(function ($item) { return array('tag' => $item->getName(), 'value'=>$item->getContent()); }, $doc->getTags())
            );
        } else {
            return array(
                'exists' => false,
                'short_description' => "",
                'long_description' => "",
                'tags' => array()
            );
        }
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

    /**
     * @return int
     */
    public function getProgress()
    {
        return $this->numFilesProcessed;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return ($this->numFilesProcessed >= $this->numFilesToProcess);
    }
}
