<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Represents a user's claim for a '.hiv' domain.
 *
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Benedikt Budig <bb@dothiv.org>
 */
class DomainClaim extends Entity {

    /**
     * Username 
     *
     * @ORM\Column(type="string")
     * @Serializer\Expose
     * @Assert\NotBlank
     */
    protected $username;

    /**
     * Domain name
     *
     * @ORM\Column(type="string")
     * @Serializer\Expose
     */
    protected $domain;

    /**
     * The token used by the owner to claim the domain
     *
     * @ORM\Column(type="string")
     * @Serializer\Expose
     * @Assert\NotBlank
     */
    protected $claimingToken;

    /* Standard getters and setters go here */

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getDomain() {
        return $this->domain;
    }

    public function setDomain($domain) {
        $this->domain = $domain;
    }

    public function getClaimingToken() {
        return $this->claimingToken;
    }

    public function setClaimingToken($token) {
        $this->claimingToken = $token;
    }
}
