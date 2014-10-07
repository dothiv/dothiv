<?php

namespace Dothiv\PayitforwardBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Repository\OrderRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\TwitterHandleValue;

class OrderRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group PayitforwardBundle
     * @group Order
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\PayitforwardBundle\Repository\OrderRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   PayitforwardBundle
     * @group   Order
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $user = new User();
        $user->setHandle('userhandle');
        $user->setEmail('someone@example.com');
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $this->getTestEntityManager()->persist($user);

        $data = array(
            'firstname'          => 'John',
            'surname'            => 'Doe',
            'email'              => 'john.doe@example.com',
            'domain'             => 'example.hiv',
            'domainDonor'        => 'Some Friend',
            'domainDonorTwitter' => '@friend',
            'type'               => 'deorg',
            'fullname'           => 'John Doe',
            'address1'           => '123 Some Street',
            'address2'           => '123 Some City',
            'country'            => 'Germany (Deutschland)',
            'vatNo'              => '1243',
            'taxNo'              => '45678',
            'domain1'            => 'super.hiv',
            'domain1Name'        => 'Super User',
            'domain1Company'     => 'Super Company',
            'domain1Twitter'     => '@super',
            'domain2'            => 'awesome.hiv',
            'domain2Name'        => 'Awesome User',
            'domain2Company'     => 'Awesome Company',
            'domain2Twitter'     => '@awesome',
            'domain3'            => 'rad.hiv',
            'domain3Name'        => 'Rad User',
            'domain3Company'     => 'Rad Company',
            'domain3Twitter'     => '@rad',
            'token'              => 'tok_14kcI342KFPpMZB0scN8KPTM',
            'liveMode'           => '0',
        );

        $order = new Order();
        $order->setUser($user);
        $order->setFirstname('John');
        $order->setSurname('Doe');
        $order->setEmail(new EmailValue('john.doe@example.com'));
        $order->setDomain(new HivDomainValue('example.hiv'));
        $order->setDomainDonor('Some Friend');
        $order->setDomainDonorTwitter(new TwitterHandleValue('@friend'));
        $order->setType('deorg');
        $order->setFullname('John Doe');
        $order->setAddress1('123 Some Street');
        $order->setAddress2('123 Some City');
        $order->setCountry('Germany (Deutschland)');
        $order->setVatNo('1243');
        $order->setTaxNo('45678');
        $order->setDomain1(new HivDomainValue('super.hiv'));
        $order->setDomain1Name('Super User');
        $order->setDomain1Company('Super Company');
        $order->setDomain1Twitter(new TwitterHandleValue('@super'));
        $order->setDomain2(new HivDomainValue('awesome.hiv'));
        $order->setDomain2Name('Awesome User');
        $order->setDomain2Company('Awesome Company');
        $order->setDomain2Twitter(new TwitterHandleValue('@awesome'));
        $order->setDomain3(new HivDomainValue('rad.hiv'));
        $order->setDomain3Name('Rad User');
        $order->setDomain3Company('Rad Company');
        $order->setDomain3Twitter(new TwitterHandleValue('@rad'));
        $order->setToken('tok_14kcI342KFPpMZB0scN8KPTM');
        $order->setLiveMode('0');
        $repo = $this->getTestObject();
        $repo->persist($order);
        $repo->flush();
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        $e = $all[0];
        foreach ($data as $k => $v) {
            $getter = 'get' . ucfirst($k);
            $this->assertEquals($v, $e->$getter(), 'Invalid ' . $k);
        }
    }

    /**
     * @return OrderRepository
     */
    protected function getTestObject()
    {
        /** @var OrderRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivPayitforwardBundle:Order');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
} 
