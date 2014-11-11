<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Repository\UserProfileChangeRepository;
use Dothiv\ValueObject\IdentValue;

class UserProfileChangeRepositoryTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\UserProfileChangeRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   UserProfileChange
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

        $change = new UserProfileChange();
        $change->setUser($user);
        $change->setProperties(array('email' => 'jane.doe@example.com'));
        $change->setToken(new IdentValue('sometoken'));
        $repo = $this->createTestObject();
        $repo->persist($change);
        $repo->flush();
        $changes = $repo->findAll();
        $this->assertEquals(1, count($changes));
        $this->assertFalse($changes[0]->getConfirmed());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   UserProfileChange
     * @group   Integration
     * @depends itShouldPersist
     */
    public function itShouldFindUninstalled()
    {
        $this->itShouldPersist();
        $repo = $this->createTestObject();
        $this->assertEquals(1, count($repo->findUnsent()));
    }

    /**
     * @return UserProfileChangeRepository
     */
    protected function createTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:UserProfileChange');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
}
