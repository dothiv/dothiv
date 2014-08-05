<?php

namespace Dothiv\BusinessBundle\Tests\Entity\ValueObject;

use Dothiv\BusinessBundle\ValueObject\HexValue;

class HexValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldParseAHexValue()
    {
        new HexValue('#336699');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAHexValue
     * @expectedException \Dothiv\BusinessBundle\Exception\InvalidArgumentException
     */
    public function itShouldNotParseAnInvalidHexValue()
    {
        new HexValue('bogus');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAHexValue
     */
    public function itShouldBeCastableToString()
    {
        $data  = '#336699';
        $value = new HexValue($data);
        $this->assertEquals($data, (string)$value, 'The value could not be casted to string');
    }

    /**
     * @test
     * @depends itShouldParseAHexValue
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldNotContainAnotherHexValueObject()
    {
        $data = '#336699';
        $v    = new HexValue(new HexValue($data));
        $this->assertEquals($data, (string)$v);
    }

    /**
     * @test
     * @depends itShouldParseAHexValue
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldExpandShortHexValues()
    {
        $data = '#af9';
        $v    = new HexValue($data);
        $this->assertEquals('#AAFF99', (string)$v);
    }
}


