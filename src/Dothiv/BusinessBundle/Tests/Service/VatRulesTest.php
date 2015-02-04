<?php


namespace Dothiv\BusinessBundle\Tests\Service;

use Dothiv\BusinessBundle\Repository\CountryRepository;
use Dothiv\BusinessBundle\Service\VatRules;
use Dothiv\ValueObject\IdentValue;

class VatRulesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group        Filter
     * @group        Service
     * @group        BusinessBundle
     */
    public function itShouldBeInstantable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Service\VatRules', $this->createTestObject());
    }

    /**
     * @param bool   $isOrg
     * @param string $country
     * @param bool   $hasVatNo
     * @param bool   $showReverseChargeNote
     * @param int    $vatPercent
     *
     * @test
     * @group        Filter
     * @group        Service
     * @group        BusinessBundle
     * @dataProvider provideTestData
     * @depends      itShouldBeInstantable
     */
    public function itShouldImplementTheCorrectRules($isOrg, $country, $hasVatNo, $showReverseChargeNote, $vatPercent)
    {
        $rules = $this->createTestObject()->getRules($isOrg, new IdentValue($country), $hasVatNo);
        $this->assertEquals($showReverseChargeNote, $rules->showReverseChargeNote());
        $this->assertEquals($vatPercent, $rules->vatPercent());
    }

    protected function createTestObject()
    {
        return new VatRules(19, new CountryRepository());
    }

    /**
     * @return array
     */
    public function provideTestData()
    {
        $hasVatNo = true;
        $isOrg    = true;
        return [
            // $isOrg, $country, $hasVatNo, $showReverseChargeNote, $vatPercent
            // Private person
            [!$isOrg, 'DE', !$hasVatNo, false, 19],
            [!$isOrg, 'LU', !$hasVatNo, false, 19],
            [!$isOrg, 'LI', !$hasVatNo, false, 19],
            [!$isOrg, 'DE', $hasVatNo, false, 19],
            [!$isOrg, 'LU', $hasVatNo, false, 19],
            [!$isOrg, 'LI', $hasVatNo, false, 19],
            // Organization …
            // … in Germany
            [$isOrg, 'DE', !$hasVatNo, false, 19],
            [$isOrg, 'DE', $hasVatNo, false, 19],
            // … in EU
            [$isOrg, 'LU', !$hasVatNo, false, 19],
            [$isOrg, 'LU', $hasVatNo, true, 0],
            // … outside EU
            [$isOrg, 'LI', !$hasVatNo, false, 0],
            [$isOrg, 'LI', $hasVatNo, false, 0]
        ];
    }
}
