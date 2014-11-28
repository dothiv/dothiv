<?php

namespace Dothiv\APIBundle\Manipulator;

use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\APIBundle\Request\DomainCollaboratorCreateRequest;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\ValueObject\IdentValue;

class DomainCollaboratorEntityManipulator implements EntityManipulatorInterface
{

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService, DomainRepositoryInterface $domainRepo)
    {
        $this->userService = $userService;
        $this->domainRepo  = $domainRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function manipulate(EntityInterface $entity, DataModelInterface $data)
    {
        if (!($data instanceof DomainCollaboratorCreateRequest)) {
            throw new InvalidArgumentException(
                sprintf('Expected $data to be a DomainCollaboratorCreateRequest, got "%s"!', get_class($data))
            );
        }
        if (!($entity instanceof DomainCollaborator)) {
            throw new InvalidArgumentException(
                sprintf('Expected $entity to be a DomainCollaborator, got "%s"!', get_class($entity))
            );
        }
        $changes   = array();
        $oldUser   = $entity->getUser();
        $oldDomain = $entity->getDomain();
        $user      = $this->userService->getOrCreateUser($data->getEmail(), $data->getFirstname(), $data->getLastname());
        $entity->setUser($user);
        if (!$user->equals($oldUser)) {
            $changes[] = new EntityPropertyChange(new IdentValue('user'), $oldUser, $user);
        }
        $this->domainRepo->getDomainByName($data->getDomain())->map(function (Domain $domain) use ($entity, $oldDomain) {
            $entity->setDomain($domain);
            if (!$domain->equals($oldDomain)) {
                $changes[] = new EntityPropertyChange(new IdentValue('domain'), $oldDomain, $domain);
            }
        });
    }
}
