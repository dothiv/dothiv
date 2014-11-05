<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Traits;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Represents a dismissable notification for a user
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\UserNotificationRepository")
 * @ORM\Table(
 *  name="UserNotification",
 *  indexes={
 *      @ORM\Index(name="charity_user_notification__user_idx", columns={"user_id"})
 *  }
 * )
 * @Serializer\ExclusionPolicy("all")
 * @Assert\Callback(methods={"isValid"})
 */
class UserNotification implements EntityInterface
{
    use Traits\CreateUpdateTime;

    /**
     * database primary key
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Assert\Type("Dothiv\BusinessBundle\Entity\User")
     * @Assert\NotBlank()
     * @var User
     */
    protected $user;

    /**
     * notification properties
     *
     * @var array
     * @ORM\Column(type="json_array", nullable=false)
     * @Assert\Type("array")
     */
    protected $properties = array();

    /**
     * Notification has been dismissed by the user.
     *
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("boolean")
     */
    protected $dismissed = false;

    /**
     * @return boolean
     */
    public function getDismissed()
    {
        return $this->dismissed;
    }

    /**
     * @param boolean $dismissed
     *
     * @return self
     */
    public function setDismissed($dismissed)
    {
        $this->dismissed = (bool)$dismissed;
        return $this;
    }

    /**
     * Dismiss the notification.
     */
    public function dismiss()
    {
        $this->setDismissed(true);
    }

    /**
     * @return int
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
        return $this->getId();
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
        return $this;
    }

    /**
     * Validates user notification
     *
     * @param ExecutionContextInterface $context
     */
    public function isValid(ExecutionContextInterface $context)
    {
        if (count($this->getProperties()) == 0) {
            $context->addViolationAt('properties', 'UserNotification has no properties!');
        }
    }
}
