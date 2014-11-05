<?php

namespace Dothiv\AdminBundle\Repository\Tests;

use Dothiv\AdminBundle\AdminEvents;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\AdminBundle\Model\EntityPropertyChange;
use Dothiv\BusinessBundle\Repository\EntityChangeRepository;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Tests\Traits;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EntityChangeRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use Traits\RepositoryTestTrait;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEventDispatcher;

    /**
     * @test
     * @group Entity
     * @group AdminBundle
     * @group EntityChange
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\EntityChangeRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   AdminBundle
     * @group   EntityChange
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')
            ->with(
                AdminEvents::ADMIN_ENTITY_CHANGE,
                $this->isInstanceOf('\Dothiv\AdminBundle\Event\EntityChangeEvent')
            )
            ->willReturnArgument(1);

        $entityChange = new EntityChange();
        $entityChange->setAuthor(new EmailValue('john.doe@exmample.com'));
        $entityChange->setEntity('\Some\Entity');
        $entityChange->setIdentifier(new IdentValue('someIdent'));
        $entityChange->addChange(new IdentValue('someProperty'), 0, 1);
        $repo = $this->getTestObject();
        $repo->persist($entityChange);
        $repo->flush();

        /** @var EntityChange[] $all */
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        $this->assertEquals('john.doe@exmample.com', (string)$all[0]->getAuthor());
        $this->assertEquals('\Some\Entity', (string)$all[0]->getEntity());
        $this->assertEquals('someIdent', (string)$all[0]->getIdentifier());
        $propertyChanges = $all[0]->getChanges();
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $propertyChanges);
        $propertyChange = $propertyChanges->get('someProperty');
        $this->assertEquals(0, $propertyChange->getOldValue());
        $this->assertEquals(1, $propertyChange->getNewValue());
    }

    /**
     * @test
     * @group   Entity
     * @group   AdminBundle
     * @group   EntityChange
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPaginate()
    {
        $repo = $this->getTestObject();
        $now  = new \DateTime();
        for ($i = 0; $i < 15; $i++) {
            $entityChange = new EntityChange();
            $entityChange->setAuthor(new EmailValue('john.doe@exmample.com'));
            $entityChange->setEntity('\Some\Entity');
            $entityChange->setIdentifier(new IdentValue('someIdent'));
            $entityChange->addChange(new IdentValue('someProperty'), 0 + $i, 1 + $i);
            $entityChange->setCreated(new \DateTime('@' . $now->getTimestamp()));
            $now->modify('+1 second');
            $repo->persist($entityChange);
        }
        $repo->flush();

        $otherChanges = $repo->getPaginated('\Some\Other\Entity', new IdentValue('someIdent'), new PaginatedQueryOptions(), new FilterQuery());
        $this->assertEquals(0, $otherChanges->getTotal());

        $changes = $repo->getPaginated('\Some\Entity', new IdentValue('someIdent'), new PaginatedQueryOptions(), new FilterQuery());
        $this->assertEquals(15, $changes->getTotal());
        $this->assertEquals(10, $changes->getResult()->count());
        /** @var EntityPropertyChange $change */
        $change = $changes->getResult()->get(0)->getChanges()->get('someProperty');
        $this->assertEquals(14, $change->getOldValue());
        $this->assertEquals(15, $change->getNewValue());

        $options = new PaginatedQueryOptions();
        $options->setOffsetKey($changes->getNextPageKey()->get());
        $changes2 = $repo->getPaginated('\Some\Entity', new IdentValue('someIdent'), $options, new FilterQuery());
        $this->assertEquals(15, $changes2->getTotal());
        $this->assertEquals(5, $changes2->getResult()->count());
        /** @var EntityPropertyChange $change2 */
        $change2 = $changes2->getResult()->get(0)->getChanges()->get('someProperty');
        $this->assertEquals(4, $change2->getOldValue());
        $this->assertEquals(5, $change2->getNewValue());
    }

    /**
     * @return EntityChangeRepository
     */
    protected function getTestObject()
    {
        /** @var EntityChangeRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:EntityChange');
        $repo->setValidator($this->testValidator);
        $repo->setEventDispatcher($this->mockEventDispatcher);
        return $repo;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->testValidator       = $this->getTestContainer()->get('validator');
        $this->mockEventDispatcher = $this->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
