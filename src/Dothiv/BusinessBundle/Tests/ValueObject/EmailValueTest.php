<?php

namespace Dothiv\BusinessBundle\Tests\Entity\ValueObject;

use Dothiv\BusinessBundle\ValueObject\EmailValue;

class EmailValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldParseAnEmail()
    {
        new EmailValue('m@click4life.hiv');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAnEmail
     * @expectedException \Dothiv\BusinessBundle\Exception\InvalidArgumentException
     */
    public function itShouldNotParseAnInvalidEmail()
    {
        new EmailValue('bogus');
    }

    /**
     * @test
     * @group   unit
     * @group   ValueObject
     * @depends itShouldParseAnEmail
     */
    public function itShouldBeCastableToString()
    {
        $data  = 'm@click4life.hiv';
        $value = new EmailValue($data);
        $this->assertEquals((string)$value, $data, 'The value could not be casted to string');
    }

    /**
     * @test
     * @depends itShouldParseAnEmail
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldNotContainAnotherUrlValueObject()
    {
        $data = 'm@click4life.hiv';
        $v    = new EmailValue(new EmailValue($data));
        $this->assertEquals((string)$v, $data);
    }

    /**
     * @test
     * @depends itShouldParseAnEmail
     * @group   unit
     * @group   ValueObject
     */
    public function itShouldReturnParts()
    {
        $value = new EmailValue('m+extension@click4life.hiv');
        $this->assertEquals('m', $value->getUser());
        $this->assertEquals('click4life.hiv', $value->getHostname());
        $this->assertEquals('extension', $value->getExtension());
    }
}


