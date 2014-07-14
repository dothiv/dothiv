<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Represents a user's claim for a '.hiv' domain.
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\DomainClaimRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Benedikt Budig <bb@dothiv.org>
 * @author Markus Tacker <m@dotHIV.org>
 */
class DomainClaim extends Entity
{
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
    protected $domainname;

    /**
     * The token used by the owner to claim the domain
     *
     * @ORM\Column(type="string")
     * @Serializer\Expose
     * @Assert\NotBlank
     */
    protected $claimingToken;

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getDomainname()
    {
        return $this->domainname;
    }

    public function setDomainname($domainname)
    {
        $this->domainname = $domainname;
    }

    public function getClaimingToken()
    {
        return $this->claimingToken;
    }

    public function setClaimingToken($token)
    {
        $this->claimingToken = $token;
    }
}
