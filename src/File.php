<?php
namespace Docblocker;

/**
 * Represents single file
 *
 * @package Docblocker
 */
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
