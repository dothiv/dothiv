<?php


namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;

interface DomainCollaboratorRepositoryInterface extends ObjectRepository, CRUDRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param DomainCollaborator $DomainCollaborator
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(DomainCollaborator $DomainCollaborator);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
