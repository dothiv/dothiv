<?php

namespace DotHiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Represents a domain that offers the same website as the associated .hiv domain.
 * 
 * Domain alternatives can be either trusted or untrusted. Only trusted alternatives
 * are publicly used.
 * 
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class DomainAlternative extends Entity {
    
    /**
     * FQDN of the alternative domain.
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     * @Assert\NotBlank
     */
    protected $domain;
    
    /**
     * Associated .hiv domain.
     * @ORM\ManyToOne(targetEntity="Domain",inversedBy="alternatives")
     * @Serializer\Expose
     */
    protected $hivDomain;
    
    /**
     * Whether this alternative is trusted.
     * @ORM\Column(type="boolean")
     * @Serializer\Expose
     */
    protected $trusted = false;
    
}