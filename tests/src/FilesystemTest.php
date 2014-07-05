<?php
namespace Docblocker;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    private $object;

    public function setUp()
    {
        $this->object = new Filesystem();
    }

    public function testGetFileMapReturnsPaths()
    {
        $map = $this->object->getFileMap(TEST_FIXTURE.'/proj');
        $this->assertContains('A.php', key($map));
    }

    public function testGetFileReturnsFileInstance()
    {
        $file = $this->object->openFile(TEST_FIXTURE.'/proj/A.php');
        $this->assertInstanceOf('\\Docblocker\\File', $file);
    }

    public function testGetFileReturnsCorrectFile()
    {
        $file = $this->object->openFile(TEST_FIXTURE.'/proj/A.php');
        $this->assertEquals(TEST_FIXTURE.'/proj/A.php', $file->getPathname());
    }
}
