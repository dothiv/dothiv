<?php

namespace DotHiv\BusinessBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Project extends Entity
{

    /**
     * short name
     *
     * @ORM\Column(type="string",length=255)
     */
    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

}
