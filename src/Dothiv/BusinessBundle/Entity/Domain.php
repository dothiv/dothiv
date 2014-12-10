<?php

namespace Dothiv\BusinessBundle\Entity;

use InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Represents a registered .hiv-Domain.
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\DomainRepository")
 * @AssertORM\UniqueEntity("name")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="domain__name",columns={"name"})})
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 * @author Markus Tacker <m@dotHIV.org>
 */
class Domain extends Entity
{
    use Traits\CreateUpdateTime;

    /**
     * FQDN, no trailing dot.
     *
     * @ORM\Column(type="string",length=255)
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     *
     * @Serializer\Expose
     */
    protected $name;

    /**
     * The owning user of the domain
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="domains")
     * @ORM\JoinColumn(nullable=true)
     * @var User|null
     */
    protected $owner;

    /**
     * Email address of the owner, as provided by registrar
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    protected $ownerEmail;

    /**
     * Name of the owner, as provided by registrar
     *
     * @ORM\Column(type="string",nullable=false)
     * @Assert\NotBlank
     * @Assert\NotNull
     * @var string
     */
    protected $ownerName;

    /**
     * This token will be used by the owner to claim the domain
     *
     * @ORM\Column(type="string",length=255,nullable=true,unique=true)
     */
    protected $token;

    /**
     * A list of (possible) banners for this domain
     *
     * @ORM\OneToMany(targetEntity="Banner",mappedBy="domain")
     */
    protected $banners;

    /**
     * The active banner for this domain, which will be actually shown
     *
     * @ORM\OneToOne(targetEntity="Banner")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @var Banner|null
     */
    protected $activeBanner;

    /**
     * The number of clicks counted for this domain.
     *
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    protected $clickcount = 0;

    /**
     * Timestamp of when the information mail hast been sent
     *
     * @var \DateTime $tokenSent
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenSent;

    /**
     * The registrar of the domain
     *
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="Registrar",inversedBy="domains")
     * @var Registrar
     */
    protected $registrar;

    /**
     * A transfer for this domain has been initiated.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $transfer = false;

    /**
     * This domain is a non-profit domain
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $nonprofit = false;

    /**
     * The domain is set up correctly
     *
     * This property is set according to the result of the last .hiv domain status check.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $live = false;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->banners = new ArrayCollection();
        parent::__construct();
    }

    /**
     * Returns the FQDN of this domain
     *
     * @return string the FQDN
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the FQDN of this domain, no trailing dot.
     *
     * @param string $fqdn
     */
    public function setName($fqdn)
    {
        $this->name = strtolower($fqdn);
    }

    /**
     * Returns the owning user of the domain
     *
     * @return User|null the owning user
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets the owner of this domain (if previously not owned),
     * transfers the domain to new owner (if previously owned),
     * removes any possible ownership (if called with 'NULL').
     *
     * @param User $newOwner
     */
    public function setOwner(User $newOwner = null)
    {
        // remove this domain from current owner's list, if anybody owns it
        if ($this->owner !== null) {
            $this->owner->getDomains()->removeElement($this);
        }

        // set new owner
        $this->owner = $newOwner;

        if ($newOwner !== null) {
            // add this domain to new owner's domains, if new owner exists
            $newOwner->getDomains()->add($this);
            // Update domain owner info
            $this->ownerEmail = $newOwner->getEmail();
            $this->ownerName  = $newOwner->getFirstname() . ' ' . $newOwner->getSurname();
        }
    }

    public function hasOwner()
    {
        return $this->owner !== null;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Claims this domain for the given user. The provided token must match the
     * claiming token.
     *
     * @param User   $newOwner
     * @param string $token
     *
     * @throws InvalidArgumentException
     * TODO: Remove token checking here …
     */
    public function claim(User $newOwner, $token)
    {
        if (!$token)
            throw new InvalidArgumentException('Given token is empty');
        if ($token !== $this->token)
            throw new InvalidArgumentException('Given token did not match');

        $this->token = null;
        $this->setOwner($newOwner);
    }

    /**
     * Returns the future user's email address as was provided by registrar.
     *
     * @return string future user's email address
     */
    public function getOwnerEmail()
    {
        return $this->ownerEmail;
    }

    /**
     * Sets the email address of the future owner as provided by registrar.
     *
     * @param string $address
     */
    public function setOwnerEmail($address)
    {
        $this->ownerEmail = $address;
    }

    /**
     * Returns a collection of all banners associated with this domain.
     *
     * @return ArrayCollection|Banner[]
     */
    public function getBanners()
    {
        return $this->banners;
    }

    /**
     * Activates the given banner for this domain. The banner will be added to
     * the list of banners if not already present.
     *
     * @param Banner|null $banner
     */
    public function setActiveBanner(Banner $banner = null)
    {
        if ($banner === null) {
            $this->activeBanner = null;
        } else {
            $this->activeBanner = $banner;
            $this->banners->contains($banner) ?: $banner->setDomain($this);
        }
    }

    /**
     * Returns the active banner.
     *
     * @return Banner|null active Banner
     */
    public function getActiveBanner()
    {
        return $this->activeBanner;
    }

    /**
     * Sets the click count
     *
     * @param int $val Current click count
     */
    public function setClickcount($val)
    {
        $this->clickcount = (int)$val;
    }

    /**
     * @return int
     */
    public function getClickcount()
    {
        return $this->clickcount;
    }

    /**
     * @param string $ownerName
     */
    public function setOwnerName($ownerName)
    {
        $this->ownerName = $ownerName;
    }

    /**
     * @return string
     */
    public function getOwnerName()
    {
        return $this->ownerName;
    }

    /**
     * @param \DateTime $informationSent
     */
    public function setTokenSent(\DateTime $informationSent)
    {
        $this->tokenSent = $informationSent;
    }

    /**
     * @return \DateTime|null
     */
    public function getTokenSent()
    {
        return $this->tokenSent;
    }

    /**
     * @param Registrar $registrar
     *
     * @return self
     */
    public function setRegistrar(Registrar $registrar)
    {
        $this->registrar = $registrar;
        return $this;
    }

    /**
     * @return Registrar
     */
    public function getRegistrar()
    {
        return $this->registrar;
    }

    /**
     * @return boolean
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * @param boolean $transfer
     *
     * @return self
     */
    public function setTransfer($transfer)
    {
        $this->transfer = (boolean)$transfer;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicId()
    {
        return $this->getName();
    }

    /**
     * Marks a domain to be in transfer
     *
     * @return self
     */
    public function transfer()
    {
        $this->setTransfer(true);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getNonprofit()
    {
        return $this->nonprofit;
    }

    /**
     * @param boolean $nonprofit
     *
     * @return self
     */
    public function setNonprofit($nonprofit)
    {
        $this->nonprofit = $nonprofit;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getLive()
    {
        return $this->live;
    }

    /**
     * @param boolean $live
     *
     * @return self
     */
    public function setLive($live)
    {
        $this->live = (boolean)$live;
        return $this;
    }

    /**
     * Compares two instance of this class
     *
     * @param Domain $domain
     *
     * @return bool
     */
    public function equals(Domain $domain = null)
    {
        if (!($domain instanceof Domain)) {
            return false;
        }
        if ($this->getName() === $domain->getName()
            && $this->getNonprofit() === $domain->getName()
            && $this->getOwner()->equals($domain->getOwner())
            && $this->getOwnerEmail() === $domain->getOwnerEmail()
            && $this->getOwnerName() === $domain->getOwnerName()
            && $this->getTransfer() === $domain->getTransfer()
            && $this->getNonprofit() === $domain->getNonprofit()
            && $this->getLive() === $domain->getLive()
            && $this->getTokenSent() === $domain->getTokenSent()
            && $this->getToken() === $domain->getToken()
            && $this->getRegistrar()->equals($domain->getRegistrar())
        ) {
            return true;
        }
        return false;
    }

}
