<?php


namespace Dothiv\ShopBundle\Tests\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Model\VatRuleModel;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\BusinessBundle\Service\VatRulesInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Model\DomainPriceModel;
use Dothiv\ShopBundle\Service\DomainPriceServiceInterface;
use Dothiv\ShopBundle\Service\InvoiceService;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

class InvoiceServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InvoiceRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInvoiceRepo;

    /**
     * @var DomainPriceServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainPriceService;

    /**
     * @var VatRulesInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockVatRulesService;

    /**
     * @test
     * @group Shop
     * @group Service
     * @group InvoiceService
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\ShopBundle\Service\InvoiceService', $this->createTestObject());
    }

    /**
     * @test
     * @group        Shop
     * @group        Service
     * @group        InvoiceService
     * @depends      itShouldBeInstantiable
     * @dataProvider invoiceTestDataProvider
     */
    public function itShouldCreateAnInvoice($vatPercent, $expectedPrice, $country, $org = null, $vatNo = null)
    {
        $order = new Order();
        $order->setDomain(new HivDomainValue('example.hiv'));
        $order->setCurrency(new IdentValue(Invoice::CURRENCY_USD));
        $order->setDuration(1);
        $order->setCountry(new IdentValue($country));
        $order->setOrganization($org);
        $order->setVatNo($vatNo);
        $order->setLocality('Some Street');
        $order->setLocality2('Some Apartment');
        $order->setCity('Some City');

        $price = new DomainPriceModel();
        $price->setNetPriceEUR(14500);
        $price->setNetPriceUSD(18000);

        $this->mockDomainPriceService->expects($this->once())->method('getPrice')
            ->with($order->getDomain())
            ->willReturn($price);

        $this->mockVatRulesService->expects($this->once())->method('getRules')
            ->with($org ? true : false, new IdentValue($country), $vatNo ? true : false)
            ->willReturn(new VatRuleModel($vatPercent, false));

        $this->mockInvoiceRepo->expects($this->once())->method('persist')
            ->willReturnSelf();

        $this->mockInvoiceRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $service = $this->createTestObject();
        $invoice = $service->createInvoice($order);
        $this->assertEquals($expectedPrice, $invoice->getTotalPrice());
        $this->assertEquals("Some Street\nSome Apartment", $invoice->getAddress1());
        $this->assertEquals("Some City", $invoice->getAddress2());
        $this->assertEquals($country, $invoice->getCountry());
    }

    public function invoiceTestDataProvider()
    {
        return [
            [19, (int)(18000 * 1.19), 'DE', null, null], // Private person, DE
            [19, (int)(18000 * 1.19), 'LU', null, ''], // Private person, EU
            [19, (int)(18000 * 1.19), 'LI', '', null], // Private person, Non-EU
            [19, (int)(18000 * 1.19), 'DE', 'ACME Inc', 'DE12345'], // DE, org
            [19, (int)(18000 * 1.19), 'LU', 'ACME Inc', null], // EU, org
            [0, 18000, 'LU', 'ACME Inc', 'DE12345'], // EU, org, not VAT id
            [0, 18000, 'LI', 'ACME Inc', null], // Non-EU Org
        ];
    }

    /**
     * @return InvoiceService
     */
    public function createTestObject()
    {
        return new InvoiceService($this->mockInvoiceRepo, $this->mockDomainPriceService, $this->mockVatRulesService);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockInvoiceRepo        = $this->getMock('\Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface');
        $this->mockDomainPriceService = $this->getMock('\Dothiv\ShopBundle\Service\DomainPriceServiceInterface');
        $this->mockVatRulesService    = $this->getMock('\Dothiv\BusinessBundle\Service\VatRulesInterface');
    }
}
