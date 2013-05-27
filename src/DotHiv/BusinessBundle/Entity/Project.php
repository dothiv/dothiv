<?php

namespace DotHiv\BusinessBundle\Entity;
use DotHiv\BusinessBundle\Enum\ProjectStatus;

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

    /**
     * Project status, as defined in enum 'ProjectStatus'
     * 
     * @ORM\Column(type="integer")
     */
    protected $status = 0;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        // define the allowed state transitions
        $transitions = array(
                ProjectStatus::DRAFT . "->" . ProjectStatus::SUBMITTED,
                ProjectStatus::DRAFT . "->" . ProjectStatus::CLOSED,
                ProjectStatus::SUBMITTED . "->" . ProjectStatus::DRAFT,
                ProjectStatus::SUBMITTED . "->" . ProjectStatus::CLOSED,
                ProjectStatus::SUBMITTED . "->" . ProjectStatus::ACCEPTED,
                ProjectStatus::ACCEPTED . "->" . ProjectStatus::PUBLISHED,
                ProjectStatus::PUBLISHED . "->" . ProjectStatus::FUNDED,
                ProjectStatus::PUBLISHED . "->" . ProjectStatus::FAILED,
                ProjectStatus::FUNDED . "->" . ProjectStatus::COMPLETED
                );
        
        // check for illegal state transition
        if ($this->status != $status && (array_search(($this->status . "->" . $status), $transitions) === false)) {
            // TODO: Gib Bad Request zurück
            throw new \InvalidArgumentException("Illegal state transition requested: $this->status -> $status");
        }
        
        $this->status = $status;
    }

}
