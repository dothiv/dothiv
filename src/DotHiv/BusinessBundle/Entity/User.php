<?php

namespace DotHiv\BusinessBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 * @Serializer\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * First name 
     *
     * @ORM\Column(type="string")
     * @Serializer\Expose
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * Last name
     *
     * @ORM\Column(type="string")
     * @Serializer\Expose
     * @Assert\NotBlank
     */
    protected $surname;

    /**
     * A list of domains owned by this user
     *
     * @ORM\OneToMany(targetEntity="Domain",mappedBy="owner")
     */
    protected $domains;

    /**
     * The user's facebook id, if facebook login is used
     *
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    protected $facebookId;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
        parent::__construct();
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * Adds a new domain to this user. Ownership of the domain
     * will be transfered.
     *
     * @param Domain $newDomain
     */
    public function addDomain(Domain $newDomain)
    {
        // let the domain class take care of everything
        $newDomain->setOwner($this);
    }

    /**
     * Removes the given domain from the user, if it was previously 
     * owned by him/her.
     *
     * @param Domain $domain
     */
    public function removeDomain(Domain $domain)
    {
        if ($this->domains->contains($domain))
            $domain->setOwner(null);
    }

    public function getFacebookId()
    {
        return $this->facebookId;
    }

    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * Updates the user's data by using the most recent data
     * from facebook. This is called every time the user logs
     * in.
     *
     * @param Array
     */
    public function setFBData($fbdata)
    {
        if ($this->username == '') {
            $this->username = $this->newRandomCode();
        }
        if (isset($fbdata['id'])) {
            $this->setFacebookId($fbdata['id']);
            $this->addRole('ROLE_FACEBOOK');
        }
        if ($this->name == '' && isset($fbdata['first_name'])) {
            $this->setName($fbdata['first_name']);
        }
        if ($this->surname == '' && isset($fbdata['last_name'])) {
            $this->setSurname($fbdata['last_name']);
        }
        if ($this->email == '' && isset($fbdata['email'])) {
            $this->setEmail($fbdata['email']);
        }
    }

    /**
     * Generates a 12 digit random code
     *
     * Used pool of characters: a-z0-9
     */
    public function newRandomCode() {
        $pool = "abcdefghijklmnopqrstuvwxyz0123456789";
        $code = "";
        while (strlen($code) < 12) {
            $code .= substr($pool, rand(0, 35), 1);
        }
        return $code;
    }
}
