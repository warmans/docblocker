<?php
namespace Docblocker;

class File extends \SplFileObject
{
    /**
     * @return string
     */
    public function getContents()
    {
        $buff = '';
        foreach ($this as $line) {
            $buff .= "$line\n";
        }
        return rtrim($buff);
    }
}
