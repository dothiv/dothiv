<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Listener;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Event\EntityEvent;
use Dothiv\CharityWebsiteBundle\Listener\SendProfileChangeConfirmationListener;

class SendProfileChangeConfirmationListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContentMailerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContentMailer;

    /**
     * @test
     * @group DothivPremiumConfiguratorBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\CharityWebsiteBundle\Listener\SendProfileChangeConfirmationListener', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivPremiumConfiguratorBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldSendEmail()
    {
        $userProfileChange = new UserProfileChange();
        $user              = new User();
        $user->setEmail('john.doe@example.com');
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $userProfileChange->setUser($user);
        $userProfileChange->setProperties(array('email' => 'newmail@example.hiv'));
        $event = new EntityEvent($userProfileChange);

        $this->mockContentMailer->expects($this->once())->method('sendContentTemplateMail')
            ->with(
                'profile.change.confirm',
                'en',
                'newmail@example.hiv', // Mail is sent to the new mail, if changed
                'John Doe',
                $this->callback(function (array $data) use ($userProfileChange) {
                    $this->assertEquals($userProfileChange, $data['change']);
                    return true;
                })
            );

        $this->getTestObject()->onEntityCreated($event);
    }

    /**
     * @return SendProfileChangeConfirmationListener
     */
    protected function getTestObject()
    {
        $command = new SendProfileChangeConfirmationListener($this->mockContentMailer);
        return $command;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockContentMailer
            = $this->getMock('\Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface');
    }
}
