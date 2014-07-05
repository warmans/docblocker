<?php
namespace tests;

use Docblocker\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    private function fakeFile()
    {
        $file = new File('php://memory', 'a+');
        foreach (func_get_args() as $arg) {
            $file->fwrite($arg);
        }
        $file->rewind();
        return $file;
    }

    public function testGetContents()
    {
        $file = $this->fakeFile('foo');
        $this->assertEquals("foo", $file->getContents());
    }
}
