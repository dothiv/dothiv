<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @Serializer\Expose
     */
    protected $handle;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Serializer\Expose
     */
    protected $mimeType;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Serializer\Expose
     */
    protected $extension;

    /**
     * @var bool
     *
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     * @Assert\Range(min=0,max=1)
     * @Serializer\Expose
     */
    protected $public = 0;

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

    /**
     * @param boolean $public
     */
    public function setPublic($public)
    {
        $this->public = (boolean)$public ? 1 : 0;
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return (boolean)$this->public;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
