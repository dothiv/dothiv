<?php

namespace Dothiv\BusinessBundle\Tests\Service;

use Dothiv\BusinessBundle\Service\Clock;

class ClockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group BusinessBundle
     * @group Service
     * @group Clock
     */
    public function itShouldBeInstantiateable()
    {
        $clock = $this->getTestObject();
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Service\Clock', $clock);
    }

    /**
     * @test
     * @group   BusinessBundle
     * @group   Service
     * @group   Clock
     * @depends itShouldBeInstantiateable
     */
    public function itShouldReturnADate()
    {
        $clock = $this->getTestObject();
        $this->assertInstanceOf('\DateTime', $clock->getNow());
    }

    /**
     * @test
     * @group   BusinessBundle
     * @group   Service
     * @group   Clock
     * @depends itShouldReturnADate
     */
    public function itShouldReturnASpecificDate()
    {
        $testClock = new \DateTime();
        $testClock->modify('+2 years');
        $clock = $this->getTestObject($testClock);
        $this->assertEquals($testClock, $clock->getNow());
    }

    /**
     * @test
     * @group   BusinessBundle
     * @group   Service
     * @group   Clock
     * @depends itShouldBeInstantiateable
     */
    public function itShouldBeImmutable()
    {
        $clock = $this->getTestObject();
        $now1  = $clock->getNow();
        $now1->modify('+1 year');
        $now2 = $clock->getNow();
        $this->assertNotEquals($now1->getTimestamp(), $now2->getTimestamp());
    }

    /**
     * @param \DateTime $date
     *
     * @return Clock
     */
    protected function getTestObject(\DateTime $date = null)
    {
        return new Clock($date);
    }
} 
