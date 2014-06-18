<?php

namespace Dothiv\BaseWebsiteBundle\Tests\Twig\Extension;

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
     * @group TwigExtension
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Twig\Extension\ContentfulTwigExtension', $this->getTestObject());
    }

    /**
     * @test
     * @group   BaseWebsiteBundle
     * @group   TwigExtension
     * @depends itShouldBeInstantiable
     */
    public function itShouldFetchABlock()
    {
        $block = new \stdClass();
        $this->mockContent->expects($this->once())->method('buildEntry')
            ->with('Block', 'example.block', 'de')
            ->will($this->returnValue($block));

        $ext    = $this->getTestObject();
        $called = false;
        foreach ($ext->getFunctions() as $func) {
            /** @var \Twig_SimpleFunction $func */
            $this->assertInstanceOf('\Twig_SimpleFunction', $func);
            if ($func->getName() == 'content') {
                $b = call_user_func($func->getCallable(), array('locale' => 'de'), 'Block', 'example.block');
                $this->assertSame($block, $b);
                $called = true;
            }
        }
        $this->assertTrue($called, 'Filter "content" was not executed.');
    }

    /**
     * @test
     * @group   BaseWebsiteBundle
     * @group   TwigExtension
     * @depends itShouldBeInstantiable
     */
    public function itShouldFetchEntries()
    {
        $quote1 = new \stdClass();
        $quote2 = new \stdClass();
        $views  = array($quote1, $quote2);
        $this->mockContent->expects($this->once())->method('buildEntries')
            ->with('Quote', 'de')
            ->will($this->returnValue($views));

        $ext    = $this->getTestObject();
        $called = false;
        foreach ($ext->getFunctions() as $func) {
            /** @var \Twig_SimpleFunction $func */
            $this->assertInstanceOf('\Twig_SimpleFunction', $func);
            if ($func->getName() == 'content') {
                $quotes = call_user_func($func->getCallable(), array('locale' => 'de'), 'Quote');
                $this->assertSame($views, $quotes);
                $called = true;
            }
        }
        $this->assertTrue($called, 'Filter "content" was not executed.');
    }

    /**
     * @test
     * @group        BaseWebsiteBundle
     * @group        TwigExtension
     * @depends      itShouldBeInstantiable
     * @dataProvider getParseBlockData
     */
    public function itShouldParseBlockBehaviour($behaviour, $search, $expected)
    {
        $block = new \stdClass();
        if ($behaviour != null) {
            $block->behaviour = $behaviour;
        }
        $ext    = $this->getTestObject();
        $called = false;
        foreach ($ext->getFilters() as $func) {
            /** @var \Twig_SimpleFunction $func */
            $this->assertInstanceOf('\Twig_SimpleFilter', $func);
            if ($func->getName() == 'behaviour') {
                $b = call_user_func($func->getCallable(), $block, $search);
                $this->assertSame($expected, $b);
                $called = true;
            }
        }
        $this->assertTrue($called, 'Filter "behaviour" was not executed.');
    }

    /**
     * @return array
     */
    public function getParseBlockData()
    {
        return array(
            array('thumbnails:person', 'thumbnails', 'person'),
            array('thumbnails', 'thumbnails', true),
            array('thumbnails', 'thumbnail', false),
            array(null, 'thumbnail', false)
        );
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
