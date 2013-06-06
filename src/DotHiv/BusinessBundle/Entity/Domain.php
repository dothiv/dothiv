<?php

namespace DotHiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Represents a registered .hiv-Domain.
 * 
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class Domain extends Entity {

    /**
     * FQDN, no trailing dot.
     * 
     * @ORM\Column(type="string",length=255)
     * @Assert\Regex("/^[^.]{1,67}\.hiv$/")
     * @Serializer\Expose
     */
    protected $name;

    /**
     * A list of domains that offer equivalent or similiar content.
     * @ORM\OneToMany(targetEntity="DomainAlternative",mappedBy="hivDomain")
     */
    protected $alternatives;

}