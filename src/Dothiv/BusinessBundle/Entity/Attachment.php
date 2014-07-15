<?php

namespace Dothiv\BusinessBundle\Entity;

use InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Dothiv\BusinessBundle\Validator\Constraints\ValidDomain;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;

/**
 * Represents an attachment
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\AttachmentRepository")
 * @AssertORM\UniqueEntity("handle")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="attachment__handle",columns={"handle"})})
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class Attachment extends Entity
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
     * @Serializer\Expose
     */
    protected $handle;

    /**
     * The user that create the attachment
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User
     */
    protected $user;

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
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

}
