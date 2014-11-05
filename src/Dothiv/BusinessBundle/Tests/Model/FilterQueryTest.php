<?php


namespace Dothiv\BusinessBundle\Tests\Model;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Model\FilterQuery;
use PhpOption\None;
use PhpOption\Option;

class FilterQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Filter
     * @group Service
     * @group BusinessBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Model\FilterQuery', $this->createTestObject());
    }

    /**
     * @test
     * @group   Filter
     * @group   Service
     * @group   BusinessBundle
     * @depends itShouldBeInstantiable
     */
    public function itShouldParseAFilterQuery()
    {
        $filterQuery = $this->createTestObject();
        $this->assertEquals(None::create(), $filterQuery->getUser());
        $user = new User();
        $filterQuery->setUser($user);
        $this->assertEquals(Option::fromValue($user), $filterQuery->getUser());
        $filterQuery->setUser(null);
        $this->assertEquals(None::create(), $filterQuery->getUser());
    }

    /**
     * @return FilterQuery
     */
    protected function createTestObject()
    {
        return new FilterQuery();
    }
}
