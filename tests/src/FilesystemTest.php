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

    public function testGetFileMapReturnsFilename()
    {
        $map = $this->object->getFileMap(TEST_FIXTURE.'/proj');

        $this->assertContains('A.php', $map);
        $this->assertContains('B.php', $map);
    }

    public function testGetFileMapReturnsFilepath()
    {
        $map = $this->object->getFileMap(TEST_FIXTURE.'/proj/');
        $this->assertContains(TEST_FIXTURE.'/proj/A.php', array_keys($map));
        $this->assertContains(TEST_FIXTURE.'/proj/Sub/B.php', array_keys($map));
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
