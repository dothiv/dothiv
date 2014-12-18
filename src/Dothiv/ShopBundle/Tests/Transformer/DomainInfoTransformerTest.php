<?php

namespace Dothiv\ShopBundle\Tests\Transformer;

use Dothiv\BusinessBundle\Entity\DomainInfo;
use Dothiv\ShopBundle\Transformer\DomainInfoTransformer;
use Dothiv\BusinessBundle\Tests\ObjectManipulator;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Component\Routing\RouterInterface;

class DomainInfoTransformerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRouter;

    /**
     * @test
     * @group AdminBundle
     * @group Transformer
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\ShopBundle\Transformer\DomainInfoTransformer', $this->createTestObject());
    }

    /**
     * @test
     * @group   AdminBundle
     * @group   Transformer
     * @depends itShouldBeInstantiable
     */
    public function itShouldTransformAnEntity()
    {
        $domainInfo = new DomainInfo();
        $domainInfo->setName(new HivDomainValue('caro.hiv'));
        $domainInfo->setRegistered(true);
        $domainInfo->setPremium(true);
        $domainInfo->setBlocked(true);
        $domainInfo->setTrademark(true);

        $this->mockRouter->expects($this->once())->method('generate')
            ->with(
                'some_route',
                array('identifier' => 'caro.hiv'),
                RouterInterface::ABSOLUTE_URL
            )
            ->willReturn('http://example.com/');

        $model = $this->createTestObject()->transform($domainInfo);
        $this->assertInstanceOf('\Dothiv\ShopBundle\Model\DomainInfoModel', $model);
        $this->assertEquals('caro.hiv', $model->getName());
        $this->assertTrue($model->getRegistered());
        $this->assertTrue($model->getPremium());
        $this->assertTrue($model->getBlocked());
        $this->assertTrue($model->getTrademark());
        $this->assertFalse($model->getAvailable());
    }

    /**
     * @return DomainInfoTransformer
     */
    public function createTestObject()
    {
        return new DomainInfoTransformer($this->mockRouter, 'some_route');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockRouter = $this->getMock('\Symfony\Component\Routing\RouterInterface');
    }
}
