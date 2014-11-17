<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\Option;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use LogicException;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * A collaborator is a user which has been given write access to a domain by the domain owner
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\DomainCollaboratorRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Assert\Callback(methods={"isValid"})
 */
class DomainCollaborator extends Entity implements CRUD\OwnerEntityInterface
{
    use Traits\CreateUpdateTime;

    /**
     * The domain
     *
     * @ORM\ManyToOne(targetEntity="Domain", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @var Domain
     */
    protected $domain;

    /**
     * The user
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @var User
     */
    protected $user;

    /**
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Validates the entity
     *
     * @param ExecutionContextInterface $context
     */
    public function isValid(ExecutionContextInterface $context)
    {
        $optionalOwner = Option::fromValue($this->getDomain()->getOwner());
        if ($optionalOwner->isEmpty()) {
            $context->addViolationAt('domain', 'Domain "%domain%" must have an owner!', array(
                '%domain%' => $this->getDomain()->getName()
            ), null);
            return;
        }
        if ($this->getDomain()->getOwner()->equals($this->getUser())) {
            $context->addViolationAt('user', 'User "%user%" must not be owner of domain "%domain%"!', array(
                '%user%'   => $this->getUser()->getHandle(),
                '%domain%' => $this->getDomain()->getName()
            ), null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        $user = Option::fromValue($this->getDomain())->map(function (Domain $domain) {
            return $domain->getOwner();
        })->getOrElse(null);
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(User $owner)
    {
        // Not supported.
    }
}
