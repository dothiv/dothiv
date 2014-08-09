<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;

/**
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\UserRepository")
 * @AssertORM\UniqueEntity("email")
 * @AssertORM\UniqueEntity("handle")
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="email",columns={"email"}),@ORM\UniqueConstraint(name="handle",columns={"handle"})})
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $handle;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $email;

    /**
     * First name
     *
     * @ORM\Column(type="string")
     * @Serializer\Expose
     * @Assert\NotBlank
     */
    protected $firstname;

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
     * @ORM\OneToMany(targetEntity="Domain", mappedBy="owner")
     * @var Domain[]|ArrayCollection
     */
    protected $domains;

    /**
     * A list of login tokens for this user
     *
     * @ORM\OneToMany(targetEntity="UserToken", mappedBy="user")
     * @var UserToken[]|ArrayCollection
     */
    protected $tokens;

    /**
     * @var string[]
     */
    protected $roles;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
        $this->tokens  = new ArrayCollection();
        $this->roles   = array('ROLE_USER');
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function setFirstname($name)
    {
        $this->firstname = $name;
    }

    /**
     * @return ArrayCollection|Domain[]
     */
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

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = strtolower($email);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     *
     * @return self
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        // pass.
    }

    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }
}
