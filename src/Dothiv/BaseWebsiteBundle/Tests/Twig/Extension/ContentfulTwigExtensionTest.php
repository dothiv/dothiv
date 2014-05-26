<?php

namespace Dothiv\BaseWebsiteBundle\Test\Twig\Extension;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BaseWebsiteBundle\Twig\Extension\ContentfulTwigExtension;

class ContentfulTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Content
     */
    private $mockContent;

    /**
     * @test
     * @group BaseWebsiteBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->getTestObject();
    }

    /**
     * @test
     * @group BaseWebsiteBundle
     */
    public function itShouldFetchABlock()
    {
        $block = new \stdClass();
        $this->mockContent->expects($this->once())->method('buildEntry')
            ->with('Block', 'example.block', 'de')
            ->will($this->returnValue($block));

        $ext = $this->getTestObject();
        foreach ($ext->getFunctions() as $func) {
            /** @var \Twig_SimpleFunction $func */
            $this->assertInstanceOf('\Twig_SimpleFunction', $func);
            $this->assertEquals('content', $func->getName());
            $b = call_user_func($func->getCallable(), array('locale' => 'de'), 'Block', 'example.block');
            $this->assertSame($block, $b);
        }
    }

    protected function getTestObject()
    {
        return new ContentfulTwigExtension($this->mockContent);
    }

    public function setUp()
    {
        $this->mockContent = $this->getMockBuilder('\Dothiv\BaseWebsiteBundle\Contentful\Content')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
