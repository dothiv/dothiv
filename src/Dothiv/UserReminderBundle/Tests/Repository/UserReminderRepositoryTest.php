<?php

namespace Dothiv\UserReminderBundle\Repository\Tests;

use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepository;
use Dothiv\ValueObject\IdentValue;

class UserReminderRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group   Entity
     * @group   UserReminderBundle
     * @group   UserReminder
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\UserReminderBundle\Repository\UserReminderRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   UserReminderBundle
     * @group   UserReminder
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldPersist()
    {
        $mockEntity = $this->getMock('\Dothiv\BusinessBundle\Entity\EntityInterface');
        $mockEntity->expects($this->once())->method('getPublicId')
            ->willReturn('som31d');

        $entity = new UserReminder();
        $entity->setIdent($mockEntity);
        $entity->setType(new IdentValue('sometype'));
        $repo = $this->createTestObject();
        $repo->persist($entity);
        $repo->flush();
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        /** @var UserReminder $created */
        $created = $all[0];
        $this->assertEquals('sometype', $created->getType()->toScalar());
        $this->assertEquals('som31d', $created->getIdent()->toScalar());
    }

    /**
     * @test
     * @group   Entity
     * @group   UserReminderBundle
     * @group   UserReminder
     * @group   Integration
     * @depends itShouldPersist
     */
    public function itShouldFindByTypeAndItem()
    {
        $mockEntity = $this->getMock('\Dothiv\BusinessBundle\Entity\EntityInterface');
        $mockEntity->expects($this->atLeast(2))->method('getPublicId')
            ->willReturn('som31d');
        $type = new IdentValue('sometype');

        $entity = new UserReminder();
        $entity->setIdent($mockEntity);
        $entity->setType($type);
        $repo = $this->createTestObject();
        $repo->persist($entity);
        $repo->flush();

        $created = $repo->findByTypeAndItem($type, $mockEntity);
        $this->assertEquals('sometype', $created[0]->getType()->toScalar());
        $this->assertEquals('som31d', $created[0]->getIdent()->toScalar());
    }

    /**
     * @return UserReminderRepository
     */
    protected function createTestObject()
    {
        /** @var UserReminderRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivUserReminderBundle:UserReminder');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
}
