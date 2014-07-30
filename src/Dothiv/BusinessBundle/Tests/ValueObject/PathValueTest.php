<?php

namespace Dothiv\BusinessBundle\Tests\Entity\ValueObject;

use Dothiv\BusinessBundle\ValueObject\PathValue;

class PathValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group   ValueObject
     * @group   unit
     */
    public function itShouldParseAPath()
    {
        new PathValue('/some/path/with/a/file.txt');
    }

    /**
     * @test
     * @group   ValueObject
     * @group   unit
     * @depends itShouldParseAPath
     */
    public function itShouldBeCastableToString()
    {
        $data = '/some/path/with/a/file.txt';
        $p    = new PathValue($data);
        $this->assertEquals($data, (string)$p, 'The value could not be casted to string');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAPath
     */
    public function itShouldNotContainAnotherPathValueObject()
    {
        $data = '/some/path/with/a/file.txt';
        $p    = new PathValue(new PathValue($data));
        $this->assertEquals($data, $p->getPathname());
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAPath
     */
    public function itShouldAddANameSuffix()
    {
        $p = PathValue::create('/some/path/with/a/file.txt')->addFilenameSuffix('@suffix');
        $this->assertEquals('/some/path/with/a/file@suffix.txt', $p->getPathname());
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAPath
     */
    public function itShouldReturnTheFileInfoObject()
    {
        $p = PathValue::create('/some/path/with/a/file.txt')->addFilenameSuffix('@suffix');
        $this->assertEquals('/some/path/with/a/file@suffix.txt', $p->getFileInfo()->getPathname());
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAPath
     */
    public function itShouldSupportIsFile()
    {
        $p = PathValue::create(__FILE__);
        $this->assertTrue($p->isFile());
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAPath
     */
    public function itShouldSupportIsDir()
    {
        $p = PathValue::create(__DIR__);
        $this->assertTrue($p->isDir());
    }
}


