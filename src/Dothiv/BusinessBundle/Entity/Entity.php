<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\MappedSuperclass
 * @Serializer\ExclusionPolicy("all")
 */
abstract class Entity implements EntityInterface
{
    /**
     * database primary key
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {

    }

    /**
     * gets the database primary key
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
     * Does *NOT* change the id
     * Throws error if any attempt is made to change the id of this entity.
     */
    public function setId($id)
    {
        if ($id != $this->id)
            throw new \Exception("Tried to change id of entity $this");
    }
}
