<?php

namespace DotHiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\MappedSuperclass
 * @Serializer\ExclusionPolicy("all")
 */
class Entity
{

    /**
     * database primary key
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     */
    protected $id;

    /**
     * gets the database primary key 
     */
    public function getId() {
        return $this->id;
    }

}
