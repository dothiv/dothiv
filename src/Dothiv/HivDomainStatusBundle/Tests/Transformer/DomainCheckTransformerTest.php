<?php

namespace Dothiv\HivDomainStatusBundle\Tests\Transformer;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\HivDomainStatusBundle\Transformer\DomainCheckTransformer;
use Dothiv\BusinessBundle\Tests\ObjectManipulator;
use Symfony\Component\Routing\RouterInterface;

class DomainCheckTransformerTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\HivDomainStatusBundle\Transformer\DomainCheckTransformer', $this->createTestObject());
    }

    /**
     * @test
     * @group   AdminBundle
     * @group   Transformer
     * @depends itShouldBeInstantiable
     */
    public function itShouldTransformAnEntity()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');

        $check = new HivDomainCheck();
        $check->setDomain($domain);
        $check->setValid(true);
        ObjectManipulator::setProtectedProperty($check, 'id', 17);

        $this->mockRouter->expects($this->once())->method('generate')
            ->with(
                'some_route',
                array('identifier' => 17),
                RouterInterface::ABSOLUTE_URL
            )
            ->willReturn('http://example.com/');

        $transformer       = $this->createTestObject();
        $domainTransformer = $this->getMock('\Dothiv\APIBundle\Transformer\EntityTransformerInterface');
        $transformer->setDomainTransformer($domainTransformer);
        $model = $transformer->transform($check);
        $this->assertInstanceOf('\Dothiv\HivDomainStatusBundle\Model\DomainCheckModel', $model);
        $this->assertEquals(true, $model->valid);
    }

    /**
     * @return DomainCheckTransformer
     */
    public function createTestObject()
    {
        return new DomainCheckTransformer($this->mockRouter, 'some_route');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockRouter = $this->getMock('\Symfony\Component\Routing\RouterInterface');
    }
}
