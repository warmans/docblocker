<?php
namespace Docblocker;

class CodeParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CodeParser
     */
    private $object;

    public function setUp()
    {
        $this->object = new CodeParser();
    }

    private function getFileMap()
    {
        return array(
            TEST_FIXTURE.'/proj/A.php'=>'A.php',
            TEST_FIXTURE.'/proj/Sub/B.php'=>'B.php',
            TEST_FIXTURE.'/proj/Sub/BInterface.php'=>'BInterface.php',
        );
    }

    public function testParseFilesResultIncludesAllClasses()
    {
        $res = $this->object->parseFiles($this->getFileMap());
        $this->assertEquals(2, count($res['classes']));
    }

    public function testParseFilesResultIncludesAllInterfaces()
    {
        $res = $this->object->parseFiles($this->getFileMap());
        $this->assertEquals(1, count($res['interfaces']));
    }

    public function testParseFilesReturnsClassName()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals('\MyProject\A', $res['classes'][0]['name']);
    }

    public function testParseFilesReturnsClassNamespace()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals('MyProject', $res['classes'][0]['namespace']);
    }

    public function testParseFilesReturnsClassDocExistsFlag()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals(true, $res['classes'][0]['doc']['exists']);
    }

    public function testParseFilesReturnsClassDocShortDescription()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals('Class A', $res['classes'][0]['doc']['short_description']);
    }

    public function testParseFilesReturnsClassDocLongDescription()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals('This class is used to test.', $res['classes'][0]['doc']['long_description']);
    }

    public function testParseFilesReturnsClassDocTags()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals('package', $res['classes'][0]['doc']['tags'][0]['tag']);
        $this->assertEquals('MyProject', $res['classes'][0]['doc']['tags'][0]['value']);
    }

    public function testParseFilesReturnsCorrectNumMethods()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals(2, count($res['classes'][0]['methods']));
    }

    public function testParseFilesReturnsMethodName()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals('aMethod', $res['classes'][0]['methods'][0]['name']);
    }

    public function testParseFilesReturnsMethodDocExists()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals(true, $res['classes'][0]['methods'][0]['doc']['exists']);
    }

    public function testParseFilesReturnsMethodDocShortDescription()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals('A Method', $res['classes'][0]['methods'][0]['doc']['short_description']);
    }

    public function testParseFilesReturnsMethodDocLongDescription()
    {
        $res = $this->object->parseFiles(array(TEST_FIXTURE.'/proj/A.php'=>'A.php'));
        $this->assertEquals('An example method', $res['classes'][0]['methods'][0]['doc']['long_description']);
    }
}
