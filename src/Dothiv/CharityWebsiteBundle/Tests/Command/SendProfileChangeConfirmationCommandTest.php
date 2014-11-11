<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Repository\UserProfileChangeRepositoryInterface;
use Dothiv\CharityWebsiteBundle\Command\SendProfileChangeConfirmationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendProfileChangeConfirmationCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInput;

    /**
     * @var OutputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOutput;

    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;

    /**
     * @var UserProfileChangeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserProfileChangeRepo;

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
        $this->assertInstanceOf('Dothiv\CharityWebsiteBundle\Command\SendProfileChangeConfirmationCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivPremiumConfiguratorBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldConfigureBanners()
    {
        $containerMap = array(
            array('dothiv.repository.user_profile_change', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockUserProfileChangeRepo),
            array('dothiv.charity.service.mailer.content', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockContentMailer),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $userProfileChange = new UserProfileChange();
        $user              = new User();
        $user->setEmail('john.doe@example.com');
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $userProfileChange->setUser($user);
        $this->mockUserProfileChangeRepo->expects($this->once())->method('findUnsent')
            ->willReturn(new ArrayCollection(array($userProfileChange)));

        $this->mockUserProfileChangeRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (UserProfileChange $change) {
                $this->assertTrue($change->getSent());
                return true;
            }))
            ->willReturnSelf();

        $this->mockContentMailer->expects($this->once())->method('sendContentTemplateMail')
            ->with(
                'profile.change.confirm',
                'en',
                'john.doe@example.com',
                'John Doe',
                $this->callback(function (array $data) use ($userProfileChange) {
                    $this->assertEquals($userProfileChange, $data['change']);
                    return true;
                })
            );

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return SendProfileChangeConfirmationCommand
     */
    protected function getTestObject()
    {
        $command = new SendProfileChangeConfirmationCommand();
        $command->setContainer($this->mockContainer);
        return $command;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockInput = $this->getMockBuilder('\Symfony\Component\Console\Input\InputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockOutput = $this->getMockBuilder('\Symfony\Component\Console\Output\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContainer = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockUserProfileChangeRepo
            = $this->getMock('\Dothiv\BusinessBundle\Repository\UserProfileChangeRepositoryInterface');
        $this->mockContentMailer
            = $this->getMock('\Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface');
    }
}
