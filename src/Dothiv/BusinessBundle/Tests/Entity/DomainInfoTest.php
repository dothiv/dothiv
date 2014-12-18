<?php

namespace Dothiv\BusinessBundle\Entity\Tests;

use Dothiv\BusinessBundle\Entity\DomainInfo;
use Dothiv\BusinessBundle\Tests\ObjectManipulator;

class DomainInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group DomainInfo
     * @group Shop
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Entity\DomainInfo', $this->getTestObject());
    }

    /**
     * @test
     * @group        Entity
     * @group        BusinessBundle
     * @group        DomainInfo
     * @group        Shop
     * @depends      itShouldBeInstantiateable
     *
     * @param bool $registered
     * @param bool $trademark
     * @param bool $blocked
     * @param bool $premium
     * @param bool $expectedAvailable
     *
     * @dataProvider getAvailableTestData
     */
    public function testAvailable($registered, $trademark, $premium, $blocked, $expectedAvailable)
    {
        $info = $this->getTestObject();
        $info->setRegistered($registered);
        $info->setTrademark($trademark);
        $info->setPremium($premium);
        $info->setBlocked($blocked);
        $this->assertEquals($expectedAvailable, $info->getAvailable());
    }

    /**
     * return array
     */
    public function getAvailableTestData()
    {
        return array(
            array(false, false, false, false, true),
            array(true, false, false, false, false),
            array(true, true, false, false, false),
            array(true, true, true, false, false),
            array(false, true, false, false, false),
            array(false, true, true, false, false),
            array(false, false, true, false, false),
            array(false, false, false, true, false),
            array(true, false, false, true, false),
            array(true, true, false, true, false),
            array(true, true, true, true, false),
            array(false, true, false, true, false),
            array(false, true, true, true, false),
            array(false, false, true, true, false),
        );
    }

    /**
     * @return DomainInfo
     */
    protected function getTestObject()
    {
        return new DomainInfo();
    }
} 
