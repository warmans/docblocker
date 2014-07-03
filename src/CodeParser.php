<?php
namespace Docblocker;

use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\FileReflector;

class CodeParser
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function parseDir($path)
    {
        $filemap = $this->filesystem->getFileMap($path);

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
        }

        return $results;
    }

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

}
