<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use PhpOption\Option;

interface RegistrarRepositoryInterface extends CRUD\PaginatedReadEntityRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param Registrar $registrar
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(Registrar $registrar);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param string $extId
     *
     * @return Option
     */
    public function findByExtId($extId);

    /**
     * Returns a registrar for the given $extId, if the registrar does not exist, it is created.
     *
     * @param string $extId
     *
     * @return Registrar
     */
    public function getByExtId($extId);
}
