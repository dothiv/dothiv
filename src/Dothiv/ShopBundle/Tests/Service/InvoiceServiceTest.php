<?php


namespace Dothiv\ShopBundle\Tests\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
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
    public function itShouldCreateAnInvoice($expectedPrice, $country, $org = null, $vatNo = null)
    {
        $order = new Order();
        $order->setDomain(new HivDomainValue('example.hiv'));
        $order->setCurrency(new IdentValue(Invoice::CURRENCY_USD));
        $order->setDuration(1);
        $order->setCountry($country);
        $order->setOrganization($org);
        $order->setVatNo($vatNo);

        $price = new DomainPriceModel();
        $price->setNetPriceEUR(14500);
        $price->setNetPriceUSD(18000);

        $this->mockDomainPriceService->expects($this->once())->method('getPrice')
            ->with($order->getDomain())
            ->willReturn($price);

        $this->mockInvoiceRepo->expects($this->once())->method('persist')
            ->willReturnSelf();

        $this->mockInvoiceRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $service = $this->createTestObject();
        $invoice = $service->createInvoice($order);
        $this->assertEquals($expectedPrice, $invoice->getTotalPrice());
    }

    public function invoiceTestDataProvider()
    {
        return [
            [(int)(18000 * 1.19), 'DE', null, null], // Private person, DE
            [(int)(18000 * 1.19), 'LU', null, null], // Private person, EU
            [(int)(18000 * 1.19), 'LI', null, null], // Private person, Non-EU
            [(int)(18000 * 1.19), 'DE', 'ACME Inc', 'DE12345'], // DE, org
            [(int)(18000 * 1.19), 'LU', 'ACME Inc', null], // EU, org
            [18000, 'LU', 'ACME Inc', 'DE12345'], // EU, org, not VAT id
            [18000, 'LI', 'ACME Inc', null], // Non-EU Org
        ];
    }

    /**
     * @return InvoiceService
     */
    public function createTestObject()
    {
        return new InvoiceService($this->mockInvoiceRepo, $this->mockDomainPriceService, 19);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockInvoiceRepo        = $this->getMock('\Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface');
        $this->mockDomainPriceService = $this->getMock('\Dothiv\ShopBundle\Service\DomainPriceServiceInterface');
    }
}
