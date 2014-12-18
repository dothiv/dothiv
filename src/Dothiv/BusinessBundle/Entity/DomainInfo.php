<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * This table holds information about registered and unregistered .hiv domains.
 *
 * Information stored about .hiv domains
 * - if a domain is registered (copied from Domain table)
 * - if a domain is a premium name
 * - if a domain is on the TMCH list
 * - if a domain is blocked otherwise (e.g. on the name collision list)
 *
 * The list can be managed via the admin dashboard.
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\DomainInfoRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class DomainInfo implements EntityInterface
{
    use Traits\CreateUpdateTime;

    /**
     * Name of the domain (including .hiv extension).
     *
     * @ORM\Id
     * @ORM\Column(type="string",length=255,nullable=false)
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Length(max=255)
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     * @var string
     */
    protected $name;

    /**
     * The domain is registered.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $registered = false;

    /**
     * The domain is a pemium domain.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $premium = false;

    /**
     * The domain is on the TMCH list.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $trademark = false;

    /**
     * The domain is blocked.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $blocked = false;

    /**
     * returns gets the database primary key
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->name;
    }

    /**
     * Returns the public identifier for this entity
     *
     * @return mixed
     */
    public function getPublicId()
    {
        return $this->getId();
    }

    /**
     * @return boolean
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * @param boolean $blocked
     */
    public function setBlocked($blocked)
    {
        $this->blocked = (bool)$blocked;
    }

    /**
     * @return HivDomainValue
     */
    public function getName()
    {
        return new HivDomainValue($this->name);
    }

    /**
     * @param HivDomainValue $name
     */
    public function setName(HivDomainValue $name)
    {
        $this->name = $name->toScalar();
    }

    /**
     * @return boolean
     */
    public function getPremium()
    {
        return $this->premium;
    }

    /**
     * @param boolean $premium
     */
    public function setPremium($premium)
    {
        $this->premium = (bool)$premium;
    }

    /**
     * @return boolean
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @param boolean $registered
     */
    public function setRegistered($registered)
    {
        $this->registered = (bool)$registered;
    }

    /**
     * @return boolean
     */
    public function getTrademark()
    {
        return $this->trademark;
    }

    /**
     * @param boolean $trademark
     */
    public function setTrademark($trademark)
    {
        $this->trademark = (bool)$trademark;
    }

    /**
     * @return bool
     */
    public function getAvailable()
    {
        return !$this->getRegistered() && !$this->getTrademark() && !$this->getPremium() && !$this->getBlocked();
    }
}
