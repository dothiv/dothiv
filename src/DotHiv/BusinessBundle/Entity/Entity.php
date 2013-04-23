<?php

namespace DotHiv\BusinessBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 */
class Entity
{

    /**
     * database primary key
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * gets the database primary key 
     */
    public function getId() {
        return $this->id;
    }

}
