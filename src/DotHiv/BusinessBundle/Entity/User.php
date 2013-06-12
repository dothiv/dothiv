<?php

namespace DotHiv\BusinessBundle\Entity;

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
     * @ORM\Column(type="string")
     * @Serializer\Expose
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose
     */
    protected $surname;

    public function __construct()
    {
        parent::__construct();
    }

    public function getSurname() {
        return $this->surname;
    }

    public function getName() {
        return $this->name;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setUsername($username) {
        if ($username !== $this->username) 
            throw new \InvalidArgumentException("Username may not be changed.");
    }
}