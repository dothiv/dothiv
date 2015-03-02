<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Dothiv\BusinessBundle\Entity\Traits;

/**
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\UserRepository")
 * @AssertORM\UniqueEntity("email")
 * @AssertORM\UniqueEntity("handle")
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="email",columns={"email"}),@ORM\UniqueConstraint(name="handle",columns={"handle"})})
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, EntityInterface
{
    use Traits\CreateUpdateTime;

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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $password;

    /**
     * First name
     *
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     */
    protected $firstname;

    /**
     * Last name
     *
     * @ORM\Column(type="string", nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank
     * @Assert\NotNull
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
     * @var string[]|Role[]
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
     * @param string[]|Role[] $roles
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
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
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

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicId()
    {
        return $this->getHandle();
    }

    /**
     * Compares two instance of this class
     *
     * @param User $user
     *
     * @return bool
     */
    public function equals(User $user = null)
    {
        if (!($user instanceof User)) {
            return false;
        }
        if ($this->getHandle() === $user->getHandle()
            && $this->getEmail() === $user->getEmail()
            && $this->getFirstname() === $user->getFirstname()
            && $this->getSurname() === $user->getSurname()
        ) {
            return true;
        }
        return false;
    }
}
