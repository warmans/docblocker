<?php
namespace Docblocker;

class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{
    /**
     * Get a fileinfo object for a given path
     *
     * @param $path
     * @param string $mode
     * @return File
     */
    public function openFile($path, $mode='a+')
    {
        return new File($path, $mode);
    }

    /**
     * Get a map of path => basenames
     *
     * @param $basePath
     * @return array
     */
    public function getFileMap($basePath)
    {
        $iterator = new \RecursiveDirectoryIterator($basePath);

        $map = array();
        foreach (new \RecursiveIteratorIterator($iterator) as $path) {
            if ($path->isFile()) {
                $pathString = $path->getPathname();
                $map[$pathString] = basename($path);
            }
        }

        //return leaves first
        return array_reverse($map);
    }
}
