<?php

namespace Dothiv\PremiumConfiguratorBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepository;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;

class SubscriptionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group PremiumConfiguratorBundle
     * @group Subscription
     */
    public function itShouldBeInstantateable()
    {
        $this->assertInstanceOf('\Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   PremiumConfiguratorBundle
     * @group   Subscription
     * @group   Integration
     * @depends itShouldBeInstantateable
     */
    public function itShouldPersist()
    {
        $subscription = $this->createSubscription();
        $repo         = $this->getTestObject();
        $repo->persist($subscription);
        $repo->flush();
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        return $repo;
    }

    protected function createSubscription()
    {
        $user = new User();
        $user->setHandle('userhandle');
        $user->setEmail('someone@example.com');
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $this->getTestEntityManager()->persist($user);

        $registrar = new Registrar();
        $registrar->setExtId('1234-AB');
        $this->getTestEntityManager()->persist($registrar);
        $domain = new Domain();
        $domain->setName('example.hiv');
        $domain->setRegistrar($registrar);
        $domain->setOwnerName('John Doe');
        $domain->setOwnerEmail('john.doe@example.com');
        $this->getTestEntityManager()->persist($domain);

        $subscription = new Subscription();
        $subscription->setUser($user);
        $subscription->setEmail(new EmailValue('john.doe@example.com'));
        $subscription->setDomain($domain);
        $subscription->setFullname('John Doe');
        $subscription->setAddress1('123 Some Street');
        $subscription->setAddress2('123 Some City');
        $subscription->setCountry(new IdentValue('DE'));
        $subscription->setVatNo('1243');
        $subscription->setToken('tok_14kcI342KFPpMZB0scN8KPTM');
        $subscription->setLiveMode('0');
        return $subscription;
    }

    /**
     * @test
     * @group   Entity
     * @group   PremiumConfiguratorBundle
     * @group   Subscription
     * @group   Integration
     * @depends itShouldBeInstantateable
     */
    public function itShouldGetById()
    {
        $subscription = $this->createSubscription();
        $repo         = $this->getTestObject();
        $repo->persist($subscription);
        $repo->flush();
        $this->assertEquals($subscription->getId(), $repo->getById($subscription->getId())->getId());
    }

    /**
     * @test
     * @group                    Entity
     * @group                    PremiumConfiguratorBundle
     * @group                    Subscription
     * @group                    Integration
     * @depends                  itShouldGetById
     * @expectedException \Dothiv\PremiumConfiguratorBundle\Exception\EntityNotFoundException
     * @expectedExceptionMessage Entity was not found.
     */
    public function getByIdShouldThrowAnExceptionIfEntityNotFound()
    {
        $this->getTestObject()->getById(17);
        $this->fail('getById() should throw an exception of entity is not found');
    }

    /**
     * @return SubscriptionRepository
     */
    protected function getTestObject()
    {
        /** @var SubscriptionRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivPremiumConfiguratorBundle:Subscription');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
}
