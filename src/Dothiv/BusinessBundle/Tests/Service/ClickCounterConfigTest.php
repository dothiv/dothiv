<?php

namespace Dothiv\BusinessBundle\Tests\Service;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Service\ClickCounterConfig;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

class ClickCounterConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Content
     */
    private $mockContent;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Client
     */
    private $mockClient;

    /**
     * @var array
     */
    private $locales = array('en', 'de', 'fr', 'es');

    /**
     * @test
     * @group Service
     * @group BusinessBundle
     * @group ClickCounterConfig
     */
    public function itShouldBeInstantiatable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Service\ClickCounterConfig', $this->createTestObject());
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return array(
            array('simple.hiv'),
            array('forward.hiv', 'http://forward.com/'),
        );
    }

    /**
     * @test
     * @group        Service
     * @group        BusinessBundle
     * @group        ClickCounterConfig
     * @depends      itShouldBeInstantiatable
     * @dataProvider getTestData
     *
     * @param string      $name
     * @param string|null $forward
     */
    public function itShouldConfigureAClickCounter($name, $forward = null)
    {
        $domain = new Domain();
        $domain->setName($name);
        $banner = new Banner();
        $banner->setDomain($domain);
        if ($forward) {
            $banner->setRedirectUrl($forward);
        }

        $expectedHeaders = array(
            'Content-type'  => 'application/json; charset=utf-8',
            'Accept'        => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(':somesecret'),
        );
        $response        = new Response(204);
        $request         = $this->getMock('\Guzzle\Http\Message\RequestInterface');
        $request->expects($this->once())->method('send')
            ->willReturn($response);

        $expectedConfig = array(
            'firstvisit'     => $banner->getPosition(),
            'secondvisit'    => $banner->getPositionAlternative(),
            'default_locale' => $banner->getLanguage(),
            'strings'        => array(),
        );
        if ($forward) {
            $expectedConfig['redirect_url'] = $forward;
        }
        foreach ($this->locales as $locale) {
            $expectedConfig['strings'][$locale] = array(
                'heading'      => 'some string',
                'shortheading' => 'some string',
                'money'        => 'some string',
            );
        }
        $expectedConfigJson = json_encode($expectedConfig);

        $this->mockClient->expects($this->once())->method('post')
            ->with(
                'https://dothiv-registry.appspot.com/config/' . $banner->getDomain()->getName(),
                $expectedHeaders,
                $expectedConfigJson
            )
            ->willReturn($request);

        $this->mockContent->expects($this->any())->method('buildEntry')
            ->willReturn((object)array('value' => 'some string'));

        $this->createTestObject()->setup($banner);
    }

    /**
     * @return ClickCounterConfig
     */
    protected function createTestObject()
    {
        $config = array(
            'baseurl' => 'https://dothiv-registry.appspot.com',
            'secret'  => 'somesecret',
            'locales' => $this->locales
        );

        $service = new ClickCounterConfig($config, $this->mockContent);
        $service->setClient($this->mockClient);
        return $service;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockContent = $this->getMockBuilder('\Dothiv\BaseWebsiteBundle\Contentful\Content')
            ->disableOriginalConstructor()->getMock();

        $this->mockClient = $this->getMock('\Guzzle\Http\ClientInterface');
    }
} 
