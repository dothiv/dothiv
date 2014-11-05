<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\BusinessBundle\Entity\UserNotification;
use Dothiv\BusinessBundle\Repository\UserNotificationRepository;

class UserNotificationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group Domain
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\UserNotificationRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   UserNotification
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldPersist()
    {
        $user = new User();
        $user->setHandle('userhandle');
        $user->setEmail('john.doe@example.com');
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $this->getTestEntityManager()->persist($user);
        $notification = new UserNotification();
        $notification->setUser($user);
        $notification->setProperties(array('some' => 'value'));
        $repo = $this->createTestObject();
        $repo->persist($notification);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @return UserNotificationRepository
     */
    protected function createTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:UserNotification');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
}
