<?php


namespace Dothiv\CharityWebsiteBundle\Tests\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\ContentInterface;
use Dothiv\CharityWebsiteBundle\Service\Mailer\DomainConfigurationMailer;
use Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapterInterface;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepositoryInterface;

class DomainConfigurationMailerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Swift_Mailer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockMailer;

    /**
     * @var ContentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContent;

    /**
     * @var ContentfulAssetAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockAssetAdapter;

    /**
     * @var ContentfulAssetRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockAssetRepo;

    /**
     * @test
     * @group CharityWebsiteBundle
     * @group Mailer
     */
    public function itShouldBeInstantiatable()
    {
        $this->assertInstanceOf('\Dothiv\CharityWebsiteBundle\Service\Mailer\DomainConfigurationMailer', $this->createTestObject());
    }

    /**
     * @test
     * @group   CharityWebsiteBundle
     * @group   Mailer
     * @depends itShouldBeInstantiatable
     */
    public function itShouldAttachIndexHtml()
    {
        // Set up data
        $data = array(
            'firstname'             => 'John',
            'surname'               => 'Doe',
            'domainName'            => 'example.hiv',
            'secondLevelDomainName' => 'example',
            'forward'               => 'http://example.com'
        );

        // Set up mocks
        $this->mockContent->expects($this->exactly(2))->method('buildEntry')
            ->withConsecutive(
                array('eMail', 'index.tpl', 'en'),
                array('Block', 'iframe.template.email', 'en') // <- this fetches the index.html template
            )
            ->willReturnOnConsecutiveCalls(
                (object)array('subject' => 'The subject', 'text' => 'Text body'),
                (object)array('text' => 'index.html content')
            );

        $this->mockMailer->expects($this->once())->method('send')
            ->with($this->callback(function (\Swift_Message $message) {
                foreach ($message->getChildren() as $child) {
                    /** @var \Swift_Mime_MimeEntity $child */
                    $this->assertEquals('index.html content', $child->getBody());
                }
                return true;
            }));

        // Run
        $this->createTestObject()->sendContentTemplateMail('index.tpl', 'en', 'john.doe@example.com', 'John Doe', $data);
    }

    /**
     * @return DomainConfigurationMailer
     */
    protected function createTestObject()
    {
        return new DomainConfigurationMailer(
            $this->mockMailer,
            $this->mockContent,
            $this->mockAssetAdapter,
            $this->mockAssetRepo,
            'domains@tld.hiv',
            '.hiv Domains'
        );
    }

    public function setup()
    {
        $this->mockMailer       = $this->getMockBuilder('\Swift_Mailer')->disableOriginalConstructor()->getMock();
        $this->mockContent      = $this->getMock('\Dothiv\BaseWebsiteBundle\Contentful\ContentInterface');
        $this->mockAssetAdapter = $this->getMock('\Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapterInterface');
        $this->mockAssetRepo    = $this->getMock('\Dothiv\ContentfulBundle\Repository\ContentfulAssetRepositoryInterface');
    }
} 
