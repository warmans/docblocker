<?php
namespace Docblocker;

class File extends \SplFileObject
{
    /**
     * Get a code helper instance with the contents of the file
     *
     * @return Helper\Code
     */
    public function getCodeHelper()
    {
        return new Helper\Code($this->getContents());
    }

    /**
     * @return string
     */
    public function getContents()
    {
        $buff = '';
        foreach ($this as $line) {
            $buff .= "$line\n";
        }
        return $buff;
    }
}
