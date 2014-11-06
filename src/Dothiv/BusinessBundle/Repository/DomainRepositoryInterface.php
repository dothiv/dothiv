<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\ValueObject\EmailValue;
use PhpOption\Option;

interface DomainRepositoryInterface extends ObjectRepository, CRUDRepositoryInterface
{
    /**
     * @param string $name
     *
     * @return Option
     */
    public function getDomainByName($name);

    /**
     * @param string $token
     *
     * @return Option
     */
    public function getDomainByToken($token);

    /**
     * Persist the entity.
     *
     * @param Domain $domain
     *
     * @return self
     */
    public function persist(Domain $domain);

    /**
     * Remove the entity.
     *
     * @param Domain $domain
     *
     * @return self
     */
    public function remove(Domain $domain);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * Returns a list of domains which click-counters have not yet been installed.
     *
     * @return ArrayCollection|Domain[]
     */
    public function findUninstalled();

    /**
     * Returns a list of domains whose owner's email is $email
     *
     * @param EmailValue $email
     *
     * @return ArrayCollection|Domain[]
     */
    public function findByOwnerEmail(EmailValue $email);
}
