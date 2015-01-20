<?php


namespace Dothiv\BusinessBundle\Tests\Model;

use Dothiv\BusinessBundle\Model\FilterQueryProperty;

class FilterQueryPropertyPropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group        Filter
     * @group        Service
     * @group        BusinessBundle
     * @dataProvider provideTestData
     */
    public function testOperatorMethods($operator, $equals, $notEquals, $greaterThan, $lessThan, $greaterOrEqualThan, $lessOrEqualThan)
    {
        $prop = new FilterQueryProperty('example', 10, $operator);
        $this->assertEquals($equals, $prop->equals());
        $this->assertEquals($notEquals, $prop->notEquals());
        $this->assertEquals($greaterThan, $prop->greaterThan());
        $this->assertEquals($lessThan, $prop->lessThan());
        $this->assertEquals($greaterOrEqualThan, $prop->greaterOrEqualThan());
        $this->assertEquals($lessOrEqualThan, $prop->lessOrEqualThan());
    }

    public function provideTestData()
    {
        return [
            [null, true, false, false, false, false, false],
            ['=', true, false, false, false, false, false],
            ['!=', false, true, false, false, false, false],
            ['>', false, false, true, false, false, false],
            ['<', false, false, false, true, false, false],
            ['>=', false, false, false, false, true, false],
            ['<=', false, false, false, false, false, true],
        ];
    }
}
