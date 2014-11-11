<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Entity\Traits;
use Dothiv\ValueObject\IdentValue;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a user profile change request which has to be confirmed
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\UserProfileChangeRepository")
 * @ORM\Table(
 *  indexes={
 *      @ORM\Index(name="user_profile_change__user_idx", columns={"user_id"})
 *  },
 *  uniqueConstraints={@ORM\UniqueConstraint(name="user_profile_change__user___token",columns={"user_id", "token"})}
 * )
 * @Serializer\ExclusionPolicy("all")
 */
class UserProfileChange extends Entity implements EntityInterface
{
    use Traits\CreateUpdateTime;

    /**
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Assert\Type("Dothiv\BusinessBundle\Entity\User")
     * @Assert\NotBlank()
     * @var User
     */
    protected $user;

    /**
     * @var IdentValue
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $token;

    /**
     * changed properties
     *
     * @var array
     * @ORM\Column(type="json_array", nullable=false)
     * @Assert\Type("array")
     */
    protected $properties = array();

    /**
     * Timestamp when the user object was retrieved.
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("\DateTime")
     */
    protected $userUpdate;

    /**
     * Notification has been confirmed by the user.
     *
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("boolean")
     */
    protected $confirmed = false;

    /**
     * Notification has been sent to the user
     *
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("boolean")
     */
    protected $sent = false;

    /**
     * @return boolean
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param boolean $confirmed
     *
     * @return self
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = (boolean)$confirmed;
        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     *
     * @return self
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @return IdentValue
     */
    public function getToken()
    {
        return new IdentValue($this->token);
    }

    /**
     * @param IdentValue $token
     *
     * @return self
     */
    public function setToken(IdentValue $token)
    {
        $this->token = $token->toScalar();
        return $this;
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
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->setUserUpdate($user->getUpdated());
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUserUpdate()
    {
        return $this->userUpdate;
    }

    /**
     * @param \DateTime|null $userUpdate
     *
     * @return self
     */
    public function setUserUpdate(\DateTime $userUpdate = null)
    {
        $this->userUpdate = $userUpdate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param boolean $sent
     *
     * @return self
     */
    public function setSent($sent)
    {
        $this->sent = (boolean)$sent;
        return $this;
    }

    /**
     * Marks the change as confirmed
     *
     * @param IdentValue $token
     *
     * @throws InvalidArgumentException
     */
    public function confirm(IdentValue $token)
    {
        if (!$this->getToken()->equals($token)) {
            throw new InvalidArgumentException(sprintf('Invalid token: "%s"!', $token));
        }
        $this->confirmed = true;
    }
}
