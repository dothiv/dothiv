<?php

namespace DotHiv\BusinessBundle\Entity;

use InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Represents a registered .hiv-Domain.
 * 
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class Domain extends Entity
{

    /**
     * FQDN, no trailing dot.
     * 
     * @ORM\Column(type="string",length=255,unique=true)
     * @Serializer\Expose
     */
    protected $name;

    /**
     * A list of domains that offer equivalent or similiar content.
     * 
     * @ORM\OneToMany(targetEntity="DomainAlternative",mappedBy="hivDomain")
     */
    protected $alternatives;

    /**
     * The owning user of the domain
     * 
     * @ORM\ManyToOne(targetEntity="User",inversedBy="domains")
     * @Serializer\Expose
     */
    protected $owner;

    /**
     * Email address of the owner, as provided by registrar
     *
     * @ORM\Column(type="string",nullable=true)
     * @Assert\NotBlank
     */
    protected $emailAddressFromRegistrar;

    /**
     * This token will be used by the owner to claim the domain
     *
     * @ORM\Column(type="string",length=255,nullable=true,unique=true)
     * @Serializer\Expose
     */
    protected $claimingToken;

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
     */
    protected $activeBanner;

    /**
     * Whether this domain shall be forwarded to dothiv's servers.
     *
     * @ORM\Column(type="boolean")
     */
    protected $dnsForward = false;

    /**
     * The number of clicks counted for this domain. For the last update
     * of this field, see $this->lastUpdate.
     *
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    protected $clickcount = 0;

    /**
     * Instant of the last update of $this->clickcount and related values.
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    protected $lastUpdate = null;

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
        $this->name = $fqdn;
    }

    /**
     * Returns the owning user of the domain
     * 
     * @return User the owning user
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
    public function setOwner(User $newOwner = NULL)
    {
        // remove this domain from current owner's list, if anybody owns it
        if ($this->owner !== null)
            $this->owner->getDomains()->removeElement($this);

        // set new owner
        $this->owner = $newOwner;

        // add this domain to new owner's domains, if new owner exists
        if ($newOwner !== null)
            $newOwner->getDomains()->add($this);
    }

    public function hasOwner()
    {
        return $this->owner !== null;
    }

    public function getClaimingToken()
    {
        return $this->claimingToken;
    }

    public function setClaimingToken($token)
    {
        $this->claimingToken = $token;
    }

    /**
     * Claims this domain for the given user. The provided token must match the
     * claiming token.
     *
     * @param User $newOwner
     * @param string $token
     * @throws InvalidArgumentException
     */
    public function claim(User $newOwner, $token)
    {
        if (empty($token))
            throw new InvalidArgumentException('Given token is empty');
        if ($token !== $this->claimingToken)
            throw new InvalidArgumentException('Given token did not match');

        $this->claimingToken = null;
        $this->setOwner($newOwner);
    }

    /**
     * Returns the future user's email address as was provided by registrar.
     *
     * @return string future user's email address
     */
    public function getEmailAddressFromRegistrar()
    {
        return $this->emailAddressFromRegistrar;
    }

    /**
     * Sets the email address of the future owner as provided by registrar.
     *
     * @param string $address
     */
    public function setEmailAddressFromRegistrar($address)
    {
        $this->emailAddressFromRegistrar = $address;
    }

    /**
     * Returns a collection of all banners associated with this domain.
     */
    public function getBanners() {
        return $this->banners;
    }

    /**
     * Activates the given banner for this domain. The banner will be added to
     * the list of banners if not already present.
     *
     * @param Banner $banner
     */
    public function setActiveBanner(Banner $banner = null) {
        if ($banner === null) {
            $this->activeBanner = null;
        } else {
            $this->activeBanner = $banner;
            $this->banners->contains($banner) ? : $banner->setDomain($this);
        }
    }

    /**
     * Returns the active banner.
     *
     * @return Banner active Banner
     */
    public function getActiveBanner() {
        return $this->activeBanner;
    }

    public function getDnsForward() {
        return $this->dnsForward;
    }

    public function setDnsForward($val) {
        $this->dnsForward = $val;
    }

    /**
     * Sets the click count and updates the lastUpdate value to now.
     * @param int $val Current click count
     */
    public function setClickcount($val) {
        $this->clickcount = $val;
        $this->lastUpdate = new \DateTime();
    }
}
