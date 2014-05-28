<?php

namespace Dothiv\BaseWebsiteBundle\Tests\Translation;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BaseWebsiteBundle\Translation\ContentfulStringsLoader;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;

class ContentfulStringsLoaderTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Translation\ContentfulStringsLoader', $this->getTestObject());
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        $s1        = new ContentfulEntry();
        $s1->code  = 'string1';
        $s1->value = 'value1';
        $s2        = new ContentfulEntry();
        $s2->code  = 'string2';
        $s2->value = 'value2';
        return array(array(array($s1, $s2)));
    }

    /**
     * @test
     * @group        BaseWebsiteBundle
     * @depends      itShouldBeInstantiable
     * @dataProvider getEntries
     *
     * @param array $entries Test entries
     */
    public function itShouldLoadStrings(array $entries)
    {
        $this->mockContent->expects($this->once())
            ->method('buildEntries')
            ->with('String', 'de')
            ->will($this->returnValue($entries));

        $loader    = $this->getTestObject();
        $catalogue = $loader->load(null, 'de');
        $strings   = $catalogue->all();
        $this->assertEquals(2, count($strings['messages']));
        $this->assertEquals('value1', $strings['messages']['string1']);
        $this->assertEquals('value2', $strings['messages']['string2']);
    }

    /**
     * @test
     * @group        BaseWebsiteBundle
     * @depends      itShouldLoadStrings
     * @dataProvider getEntries
     *
     * @param array $entries Test entries
     */
    public function itShouldLoadKeysAsStrings(array $entries)
    {
        $this->mockContent->expects($this->once())
            ->method('buildEntries')
            ->with('String', 'ky')
            ->will($this->returnValue($entries));

        $loader    = $this->getTestObject();
        $catalogue = $loader->load(null, 'ky');
        $strings   = $catalogue->all();
        $this->assertEquals('string1', $strings['messages']['string1']);
        $this->assertEquals('string2', $strings['messages']['string2']);
    }

    protected function getTestObject()
    {
        return new ContentfulStringsLoader($this->mockContent, 'String', 'ky');
    }

    public function setUp()
    {
        $this->mockContent = $this->getMockBuilder('\Dothiv\BaseWebsiteBundle\Contentful\Content')
            ->disableOriginalConstructor()
            ->getMock();
    }
} 
