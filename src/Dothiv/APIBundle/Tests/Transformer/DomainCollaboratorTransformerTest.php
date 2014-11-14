<?php

namespace Dothiv\APIBundle\Tests\Transformer;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use Dothiv\APIBundle\Transformer\DomainCollaboratorTransformer;
use Dothiv\BusinessBundle\Tests\ObjectManipulator;
use Symfony\Component\Routing\RouterInterface;

class DomainCollaboratorTransformerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRouter;

    /**
     * @test
     * @group AdminBundle
     * @group Transformer
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\APIBundle\Transformer\DomainCollaboratorTransformer', $this->createTestObject());
    }

    /**
     * @test
     * @group   AdminBundle
     * @group   Transformer
     * @depends itShouldBeInstantiable
     */
    public function itShouldTransformAnEntity()
    {
        $friend = new User();
        $friend->setHandle('friendhandle');
        $friend->setEmail('jane.doe@example.com');
        $friend->setFirstname('Jane');
        $friend->setSurname('Doe');
        ObjectManipulator::setProtectedProperty($friend, 'id', 42);

        $domain = new Domain();
        $domain->setName('example.hiv');
        ObjectManipulator::setProtectedProperty($friend, 'id', 7);

        $collaborator = new DomainCollaborator();
        $collaborator->setUser($friend);
        $collaborator->setDomain($domain);
        ObjectManipulator::setProtectedProperty($collaborator, 'id', 17);

        $this->mockRouter->expects($this->once())->method('generate')
            ->with(
                'some_route',
                array('identifier' => 17, 'name' => $domain->getName()),
                RouterInterface::ABSOLUTE_URL
            )
            ->willReturn('http://example.com/');

        $transformer       = $this->createTestObject();
        $domainTransformer = $this->getMock('\Dothiv\APIBundle\Transformer\EntityTransformerInterface');
        $userTransformer   = $this->getMock('\Dothiv\APIBundle\Transformer\EntityTransformerInterface');
        $transformer->setDomainTransformer($domainTransformer);
        $transformer->setUserTransformer($userTransformer);
        $model = $transformer->transform($collaborator);
        $this->assertInstanceOf('\Dothiv\APIBundle\Model\DomainCollaboratorModel', $model);
    }

    /**
     * @return DomainCollaboratorTransformer
     */
    public function createTestObject()
    {
        return new DomainCollaboratorTransformer($this->mockRouter, 'some_route');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockRouter = $this->getMock('\Symfony\Component\Routing\RouterInterface');
    }
}
