<?php

namespace Dothiv\PayitforwardBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Repository\OrderRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\TwitterHandleValue;
use PhpOption\Some;

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
        $data = array(
            'firstname'          => 'John',
            'surname'            => 'Doe',
            'email'              => 'john.doe@example.com',
            'domain'             => 'example.hiv',
            'domainDonor'        => 'Some Friend',
            'domainDonorTwitter' => '@friend',
            'fullname'           => 'John Doe',
            'address1'           => '123 Some Street',
            'address2'           => '123 Some City',
            'organization'       => 'ACME Inc.',
            'country'            => new IdentValue('DE'),
            'vatNo'              => '1243',
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

        $order = $this->createOrder();

        $repo = $this->getTestObject();
        $repo->persist($order);
        $repo->flush();
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        $e = $all[0];
        foreach ($data as $k => $v) {
            $getter = 'get' . ucfirst($k);
            $gv     = $e->$getter();
            if ($gv instanceof Some) {
                $gv = $gv->get();
            }
            $this->assertEquals($v, $gv, 'Invalid ' . $k);
        }

        return $repo;
    }

    protected function createOrder()
    {
        $user = new User();
        $user->setHandle('userhandle');
        $user->setEmail('someone@example.com');
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $this->getTestEntityManager()->persist($user);

        $order = new Order();
        $order->setUser($user);
        $order->setFirstname('John');
        $order->setSurname('Doe');
        $order->setEmail(new EmailValue('john.doe@example.com'));
        $order->setDomain(new HivDomainValue('example.hiv'));
        $order->setDomainDonor('Some Friend');
        $order->setDomainDonorTwitter(new TwitterHandleValue('@friend'));
        $order->setFullname('John Doe');
        $order->setAddress1('123 Some Street');
        $order->setAddress2('123 Some City');
        $order->setOrganization('ACME Inc.');
        $order->setCountry(new IdentValue('DE'));
        $order->setVatNo('1243');
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
        return $order;
    }

    /**
     * @test
     * @group   Entity
     * @group   PayitforwardBundle
     * @group   Order
     * @group   Integration
     * @depends itShouldPersist
     */
    public function itShouldReturnNewOrders()
    {
        $order = $this->createOrder();
        $repo  = $this->getTestObject();
        $repo->persist($order);
        $repo->flush();
        $newOrders = $repo->findNew();
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $newOrders);
        $this->assertEquals(1, $newOrders->count());
        $this->assertEquals($order, $newOrders->first());

        $order->setCharge('abc');
        $repo->persist($order)->flush();
        $newOrders2 = $repo->findNew();
        $this->assertEquals(0, $newOrders2->count());
    }

    /**
     * @test
     * @group   Entity
     * @group   PayitforwardBundle
     * @group   Order
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldGetById()
    {
        $order = $this->createOrder();
        $repo  = $this->getTestObject();
        $repo->persist($order);
        $repo->flush();
        $this->assertEquals($order->getId(), $repo->getById($order->getId())->getId());
    }

    /**
     * @test
     * @group                    Entity
     * @group                    PayitforwardBundle
     * @group                    Order
     * @group                    Integration
     * @depends                  itShouldGetById
     * @expectedException \Dothiv\PayitforwardBundle\Exception\EntityNotFoundException
     * @expectedExceptionMessage Entity was not found.
     */
    public function getByIdShouldThrowAnExceptionIfEntityNotFound()
    {
        $this->getTestObject()->getById(17);
        $this->fail('getById() should throw an exception of entity is not found');
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
