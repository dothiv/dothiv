<?php

namespace Dothiv\BusinessBundle\Tests\Functional;

use Dothiv\BusinessBundle\Entity\UserNotification;
use Dothiv\BusinessBundle\Service\IRegistration;
use Dothiv\BusinessBundle\Tests\Fixtures\NewUserEmailChangeNotificationTestFixture;
use Dothiv\BusinessBundle\Tests\Traits;

/**
 * Tests that new users created have a notification for them
 */
class NewUserEmailChangeNotificationTest extends \PHPUnit_Framework_TestCase
{
    use Traits\RepositoryTestTrait;

    /**
     * @test
     * @group Integration
     * @group Functional
     * @group BusinessBundle
     */
    public function itShouldCreateANotification()
    {
        $this->loadFixture(new NewUserEmailChangeNotificationTestFixture());

        /** @var IRegistration $registrationService */
        $registrationService = $this->getTestContainer()->get('registration');
        $registrationService->registered('example.hiv', 'john.doe@example.com', 'John Doe', '1234-AB');

        $notifications = $this->getTestContainer()->get('dothiv.repository.user_notification')->findAll();
        $this->assertEquals(1, count($notifications));
        /** @var UserNotification $notification */
        $notification = $notifications[0];
        $this->assertEquals('john.doe@example.com', $notification->getUser()->getEmail());
        $this->assertFalse($notification->getDismissed());
        $this->assertEquals(array('role' => 'charity.change_email'), $notification->getProperties());
    }
}
