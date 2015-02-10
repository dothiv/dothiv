<?php

namespace Dothiv\LandingpageBundle\Tests\Transformer;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Transformer\LandingpageConfigurationTransformer;
use Dothiv\ValueObject\IdentValue;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\RouterInterface;

class LandingpageConfigurationTransformerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObjectProphecy
     */
    private $mockRouter;

    /**
     * @test
     * @group LandingpageBundle
     * @group Transformer
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Transformer\LandingpageConfigurationTransformer', $this->createTestObject());
    }

    /**
     * @test
     * @group   LandingpageBundle
     * @group   Transformer
     * @depends itShouldBeInstantiable
     */
    public function itShouldTransformAnEntity()
    {
        $domain = new Domain();
        $domain->setName('caro4life.hiv');
        $config = new LandingpageConfiguration();
        $config->setName('Caro');
        $config->setText('Some text');
        $config->setLanguage(new IdentValue('en'));
        $config->setClickCounter(true);
        $config->setDomain($domain);

        $this->mockRouter->generate('some_route', array('identifier' => 'caro4life.hiv'), RouterInterface::ABSOLUTE_URL)
            ->willReturn('http://example.com/')
            ->shouldBeCalled();

        $model = $this->createTestObject()->transform($config);
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Model\LandingpageConfigurationModel', $model);
        $this->assertEquals('http://jsonld.click4life.hiv/LandingpageConfiguration', $model->getJsonLdContext()->toScalar());
        $this->assertEquals("Caro", $model->getName());
        $this->assertEquals("en", $model->getLanguage());
        $this->assertEquals("Some text", $model->getText());
        $this->assertTrue($model->getClickCounter());
    }

    /**
     * @return LandingpageConfigurationTransformer
     */
    public function createTestObject()
    {
        return new LandingpageConfigurationTransformer($this->mockRouter->reveal(), 'some_route');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockRouter = $this->prophesize('\Symfony\Component\Routing\RouterInterface');
    }
}
