<?php

namespace Dothiv\BaseWebsiteBundle\Tests\Controller;

use Doctrine\Common\Cache\ArrayCache;
use Dothiv\BaseWebsiteBundle\BaseWebsiteBundleEvents;
use Dothiv\BaseWebsiteBundle\Cache\RequestLastModifiedCache;
use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BaseWebsiteBundle\Contentful\ViewBuilder;
use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Dothiv\BaseWebsiteBundle\Event\ContentfulViewEvent;
use Dothiv\ContentfulBundle\ContentfulEvents;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class PageControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Content
     */
    private $mockContent;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ViewBuilder
     */
    private $mockViewBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $mockRenderer;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @test
     * @group BaseWebsiteBundle
     * @group Controller
     */
    public function itShouldBeInstantiateable()
    {
        $controller = $this->getTestObject();
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Controller\PageController', $controller);
    }

    /**
     * @test
     * @group   BaseWebsiteBundle
     * @group   Controller
     * @depends itShouldBeInstantiateable
     */
    public function itShouldSendLastModifiedHeader()
    {
        $controller = $this->getTestObject();

        $date_older   = new \DateTime('2014-06-02T08:06:17Z');
        $date_newer   = new \DateTime('2014-06-03T08:06:17Z');
        $date_updated = new \DateTime('2014-06-04T08:06:17Z');

        $childView                   = new \stdClass();
        $childView->cfMeta           = array(
            'itemId'    => 'childItem',
            'updatedAt' => $date_newer
        );
        $parentView                  = new \stdClass();
        $parentView->cfMeta          = array(
            'itemId'    => 'parentItem',
            'updatedAt' => $date_older
        );
        $parentView->children        = array($childView);
        $updatedParentView           = new \stdClass();
        $updatedParentView->cfMeta   = array(
            'itemId'    => 'parentItem',
            'updatedAt' => $date_updated
        );
        $updatedParentView->children = array($childView);

        // It should build the view for the page.
        $dispatcher = $this->dispatcher;
        $this->mockContent->expects($this->at(0))->method('buildEntry')
            ->with('Page', 'test', 'en')
            ->will(
                $this->returnCallback(function () use ($dispatcher, $parentView, $childView) {
                        $dispatcher->dispatch(BaseWebsiteBundleEvents::CONTENTFUL_VIEW_CREATE, new ContentfulViewEvent($parentView));
                        $dispatcher->dispatch(BaseWebsiteBundleEvents::CONTENTFUL_VIEW_CREATE, new ContentfulViewEvent($childView));
                        return $parentView;
                    }
                ));

        $this->mockContent->expects($this->at(1))->method('buildEntry')
            ->with('Page', 'test', 'en')
            ->will(
                $this->returnCallback(function () use ($dispatcher, $updatedParentView, $childView) {
                        $dispatcher->dispatch(BaseWebsiteBundleEvents::CONTENTFUL_VIEW_CREATE, new ContentfulViewEvent($updatedParentView));
                        $dispatcher->dispatch(BaseWebsiteBundleEvents::CONTENTFUL_VIEW_CREATE, new ContentfulViewEvent($childView));
                        return $updatedParentView;
                    }
                ));
        // Get uncached Response
        $request  = new Request();
        $response = $controller->pageAction(
            $request,
            'en',
            'test'
        );
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(200, $response->getStatusCode(), 'Request without If-Modified-Since should return status 200!');
        $this->assertTrue($response->headers->hasCacheControlDirective('public'), 'It should be public!');
        $this->assertTrue($response->isCacheable(), 'It should be cacheable!');
        $this->assertEquals($date_newer, $response->getLastModified(), 'The last modified date should be that of the newest entry!');

        // Get cached response
        $request = new Request();
        $date_newer->setTimezone(new \DateTimeZone('UTC'));
        $request->headers->add(array('If-Modified-Since' => $date_newer->format('D, d M Y H:i:s') . ' GMT'));
        $response = $controller->pageAction(
            $request,
            'en',
            'test'
        );
        $this->assertEquals(304, $response->getStatusCode(), 'Request with If-Modified-Since should return status 304!');

        // Content update should return uncached response
        $childUpdate = new ContentfulEntry();
        $childUpdate->setId('childItem');
        $childUpdate->setUpdatedAt($date_updated);
        $this->dispatcher->dispatch(ContentfulEvents::ENTRY_SYNC, new ContentfulEntryEvent($childUpdate));
        $request  = new Request();
        $response = $controller->pageAction(
            $request,
            'en',
            'test'
        );
        $this->assertEquals(200, $response->getStatusCode(), 'After update it should return a new version.');
        $this->assertEquals($date_updated, $response->getLastModified(), 'The last modified date should be that of the updated entry!');
    }

    /**
     * @return PageController
     */
    protected function getTestObject()
    {
        $lmc = new RequestLastModifiedCache(new ArrayCache());
        $this->dispatcher->addListener(
            BaseWebsiteBundleEvents::CONTENTFUL_VIEW_CREATE, array($lmc, 'onViewCreate')
        );
        $this->dispatcher->addListener(
            ContentfulEvents::ENTRY_SYNC, array($lmc, 'onEntryUpdate')
        );
        return new PageController($lmc, $this->mockRenderer, $this->mockContent, 'BaseWebsiteBundle');
    }


    /**
     * {@inheritdoc}
     */
    public function setUp()
    {

        $this->mockViewBuilder = $this->getMockBuilder('\Dothiv\BaseWebsiteBundle\Contentful\ViewBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dispatcher      = new EventDispatcher();

        $this->mockContent = $this->getMockBuilder('\Dothiv\BaseWebsiteBundle\Contentful\Content')
            ->disableOriginalConstructor()
            ->getMock();

        $mockTemplateNameParser = $this->getMockBuilder('\Symfony\Component\Templating\TemplateNameParserInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockFileLocator = $this->getMockBuilder('\Symfony\Component\Config\FileLocatorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockRenderer = new TwigEngine(
            new \Twig_Environment(new \Twig_Loader_String()),
            $mockTemplateNameParser,
            $mockFileLocator
        );
    }
}