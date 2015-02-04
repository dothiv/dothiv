<?php

namespace Dothiv\PremiumConfiguratorBundle\Tests\Service;

use Dothiv\BusinessBundle\Model\VatRuleModel;
use Dothiv\BusinessBundle\Service\VatRulesInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Service\InvoiceService;
use Dothiv\ValueObject\IdentValue;
use PhpOption\None;

class InvoiceServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @$\PHPUnit_Framework_MockObject_MockObject|InvoiceRepositoryInterface
     */
    private $mockInvoiceRepo;

    /**
     * @var VatRulesInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockVatRulesService;

    /**
     * @test
     * @group Invoice
     * @group PremiumConfiguratorBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\PremiumConfiguratorBundle\Service\InvoiceService', $this->createTestObject());
    }

    /**
     * @param bool   $isOrg
     * @param string $country
     * @param bool   $hasVatNo
     * @param bool   $showReverseChargeNote
     * @param int    $vatPercent
     * @param int    $vatPrice
     * @param int    $totalPrice
     *
     * @test
     * @group        Invoice
     * @group        PremiumConfiguratorBundle
     * @depends      itShouldBeInstantiable
     * @dataProvider subscriptionData
     */
    public function itShouldCreateAnInvoiceForSubscription($isOrg, $country, $hasVatNo, $showReverseChargeNote, $vatPercent, $vatPrice, $totalPrice)
    {
        // Set up mocks
        $this->mockInvoiceRepo->expects($this->once())->method('persist')
            ->with($this->isInstanceOf('\Dothiv\BusinessBundle\Entity\Invoice'))
            ->willReturnSelf();
        $this->mockInvoiceRepo->expects($this->once())->method('flush');

        $this->mockVatRulesService->expects($this->once())->method('getRules')
            ->with($isOrg, new IdentValue($country), $hasVatNo)
            ->willReturn(new VatRuleModel($vatPercent, $showReverseChargeNote));

        // Set up subscription
        $subscription = new Subscription();
        $address1     = 'Some street';
        $address2     = 'Some locality';
        $fullname     = 'John Doe';
        $subscription->setFullname($fullname);
        $subscription->setAddress1($address1);
        $subscription->setAddress2($address2);
        $subscription->setCountry(new IdentValue($country));
        if ($isOrg) {
            $subscription->setOrganization('ACME Inc.');
        }
        if ($hasVatNo) {
            $subscription->setVatNo('DE236824699');
        }
        $intervalStart = new \DateTime('2014-10-01T12:34:56+02:00');

        // Run
        $service = $this->createTestObject();
        $invoice = $service->createInvoiceForSubscription($subscription, $intervalStart);
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Entity\Invoice', $invoice);
        $this->assertEquals($fullname, $invoice->getFullname());
        $this->assertEquals($address1, $invoice->getAddress1());
        $this->assertEquals($address2, $invoice->getAddress2());
        $this->assertEquals($country, $invoice->getCountry());
        if ($hasVatNo) {
            $this->assertEquals('DE236824699', $invoice->getVatNo()->get());
        } else {
            $this->assertEquals(None::create(), $invoice->getVatNo());
        }
        $this->assertEquals($vatPercent, $invoice->getVatPercent());
        $this->assertEquals($vatPrice, $invoice->getVatPrice());
        $this->assertEquals($totalPrice, $invoice->getTotalPrice());
        $this->assertEquals(1000, $invoice->getItemPrice());
        $expectedDescription = 'Click-Counter premium subscription (monthly Price Plan) from Oct. 1st 2014 to Oct. 31st 2014.';
        $this->assertEquals($expectedDescription, $invoice->getItemDescription());
    }

    /**
     * Provides testdata for itShouldCreateAnInvoiceForSubscription
     *
     * @return array
     */
    public function subscriptionData()
    {
        $hasVatNo = true;
        $isOrg    = true;
        return [
            // $isOrg, $country, $hasVatNo, $showReverseChargeNote, $vatPercent, $vatPrice, $totalPrice
            // Private person
            [!$isOrg, 'DE', !$hasVatNo, false, 19, 190, 1190],
            [!$isOrg, 'LU', !$hasVatNo, false, 19, 190, 1190],
            [!$isOrg, 'LI', !$hasVatNo, false, 19, 190, 1190],
            [!$isOrg, 'DE', $hasVatNo, false, 19, 190, 1190],
            [!$isOrg, 'LU', $hasVatNo, false, 19, 190, 1190],
            [!$isOrg, 'LI', $hasVatNo, false, 19, 190, 1190],
            // Organization …
            // … in Germany
            [$isOrg, 'DE', !$hasVatNo, false, 19, 190, 1190],
            [$isOrg, 'DE', $hasVatNo, false, 19, 190, 1190],
            // … in EU
            [$isOrg, 'LU', !$hasVatNo, false, 19, 190, 1190],
            [$isOrg, 'LU', $hasVatNo, true, 0, 0, 1000],
            // … outside EU
            [$isOrg, 'LI', !$hasVatNo, false, 0, 0, 1000],
            [$isOrg, 'LI', $hasVatNo, false, 0, 0, 1000]
        ];
    }

    protected function createTestObject()
    {
        return new InvoiceService($this->mockInvoiceRepo, 1000, $this->mockVatRulesService);
    }

    public function setUp()
    {
        $this->mockInvoiceRepo     = $this->getMock('\Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface');
        $this->mockVatRulesService = $this->getMock('\Dothiv\BusinessBundle\Service\VatRulesInterface');
    }
}
