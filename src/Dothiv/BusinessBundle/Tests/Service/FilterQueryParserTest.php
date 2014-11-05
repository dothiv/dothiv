<?php


namespace Dothiv\BusinessBundle\Tests\Service;

use Dothiv\BusinessBundle\Service\FilterQueryParser;
use PhpOption\None;

class FilterQueryParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Filter
     * @group Service
     * @group BusinessBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Service\FilterQueryParser', $this->createTestObject());
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
        $filterQuery = $this->createTestObject()->parse('acme @transfer{1}');
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Model\FilterQuery', $filterQuery);
        $this->assertEquals('acme', $filterQuery->getTerm()->get());
        $this->assertEquals('1', $filterQuery->getProperty('transfer')->get());
    }

    /**
     * @return FilterQueryParser
     */
    protected function createTestObject()
    {
        return new FilterQueryParser();
    }
}
