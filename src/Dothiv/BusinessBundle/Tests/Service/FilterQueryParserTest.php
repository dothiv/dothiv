<?php


namespace Dothiv\BusinessBundle\Tests\Service;

use Dothiv\BusinessBundle\Model\FilterQueryProperty;
use Dothiv\BusinessBundle\Service\FilterQueryParser;

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
        /** @var FilterQueryProperty $prop */
        $prop = $filterQuery->getProperty('transfer')->get();
        $this->assertEquals('transfer', $prop->getName());
        $this->assertEquals('1', $prop->getValue());
        $this->assertEquals(FilterQueryProperty::OPERATOR_EQUALS, $prop->getOperator());
    }

    /**
     * @test
     * @group        Filter
     * @group        Service
     * @group        BusinessBundle
     * @depends      itShouldParseAFilterQuery
     * @dataProvider getQueriesWithComparisonOperator
     */
    public function itShouldParseAFilterQueryWithComparisonOperator($query, $expectedValue, $expectedOperator)
    {
        $filterQuery = $this->createTestObject()->parse($query);
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Model\FilterQuery', $filterQuery);
        /** @var FilterQueryProperty $prop */
        $prop = $filterQuery->getProperty('example')->get();
        $this->assertEquals($expectedValue, $prop->getValue());
        $this->assertEquals($expectedOperator, $prop->getOperator());
    }

    public function getQueriesWithComparisonOperator()
    {
        return [
            ['@example{10}', '10', '='], // Default
            ['@example{=10}', '10', '='],
            ['@example{!=10}', '10', '!='],
            ['@example{>10}', '10', '>'],
            ['@example{<10}', '10', '<'],
            ['@example{<=10}', '10', '<='],
            ['@example{>=10}', '10', '>='],
        ];
    }

    /**
     * @return FilterQueryParser
     */
    protected function createTestObject()
    {
        return new FilterQueryParser();
    }
}
