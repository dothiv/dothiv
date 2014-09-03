<?php

namespace Dothiv\BusinessBundle\Tests\Entity\ValueObject;

use Dothiv\BusinessBundle\ValueObject\HivDomainValue;

class HivDomainValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldParseADomain()
    {
        new HivDomainValue('click4life.hiv');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseADomain
     * @expectedException \Dothiv\BusinessBundle\Exception\InvalidArgumentException
     */
    public function itShouldNotParseAnInvalidDomain()
    {
        new HivDomainValue('bogus');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseADomain
     */
    public function itShouldBeCastableToString()
    {
        $data  = 'click4life.hiv';
        $value = new HivDomainValue($data);
        $this->assertEquals((string)$value, $data, 'The value could not be casted to string');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseADomain
     */
    public function itShouldBeLowerCase()
    {
        $data  = 'Click4Life.HIV';
        $value = new HivDomainValue($data);
        $this->assertEquals((string)$value, strtolower($data));
    }

    /**
     * @test
     * @depends itShouldParseADomain
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldNotContainAnotherValueObject()
    {
        $data = 'click4life.hiv';
        $v    = new HivDomainValue(new HivDomainValue($data));
        $this->assertEquals((string)$v, $data);
    }

    /**
     * @test
     * @depends itShouldParseADomain
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldReturnParts()
    {
        $value = new HivDomainValue('click4life.hiv');
        $this->assertEquals('click4life', $value->getSecondLevel());
    }

    /**
     * @test
     * @depends      itShouldParseADomain
     * @group        unit
     * @group        ValueObject
     * @dataProvider provideIdnConversionData
     */
    public function itShouldConvertIdnToUTF8($idn, $utf8)
    {
        $domain = new HivDomainValue($idn);
        $this->assertEquals($utf8, $domain->toUTF8());
    }

    public function provideIdnConversionData()
    {
        return array(
            array('example.hiv', 'example.hiv'),
            array('xn--brger-kva.hiv', 'bürger.hiv')
        );
    }
}


