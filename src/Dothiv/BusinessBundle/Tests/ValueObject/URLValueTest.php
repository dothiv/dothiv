<?php

namespace Dothiv\BusinessBundle\Tests\Entity\ValueObject;

use Dothiv\BusinessBundle\ValueObject\URLValue;

class URLValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldParseAnURL()
    {
        new URLValue('https://click4life.hiv/');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAnURL
     * @expectedException \Dothiv\BusinessBundle\Exception\InvalidArgumentException
     */
    public function itShouldNotParseAnInvalidURL()
    {
        new URLValue('bogus');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAnURL
     */
    public function itShouldBeCastableToString()
    {
        $data  = 'https://click4life.hiv/';
        $value = new URLValue($data);
        $this->assertEquals((string)$value, $data, 'The value could not be casted to string');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAnURL
     */
    public function urlsAlwaysShouldHaveATrailingSlash()
    {
        $d = new URLValue('https://click4life.hiv');
        $this->assertEquals('https://click4life.hiv/', (string)$d);
    }

    /**
     * @test
     * @depends itShouldParseAnURL
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldNotContainAnotherUrlValueObject()
    {
        $data = 'https://click4life.hiv/';
        $v    = new URLValue(new URLValue($data));
        $this->assertEquals((string)$v, $data);
    }

    /**
     * @test
     * @depends itShouldParseAnURL
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldReturnParts()
    {
        $value = new URLValue('https://www.example.com/directory/index.php?query=true#fragment');
        $this->assertEquals('https', $value->getScheme());
        $this->assertEquals('www.example.com', $value->getHostname());
        $this->assertEquals('/directory/index.php', $value->getPath());
        $this->assertEquals('query=true', $value->getQuery());
        $this->assertEquals('fragment', $value->getFragment());
    }
}


