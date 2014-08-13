<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Represents a premium domain bid
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\PremiumBidRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class PremiumBid extends Entity
{
    use Traits\CreateUpdateTime;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * FQDN, no trailing dot.
     *
     * @ORM\Column(type="string",length=255,nullable=false)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     * @Serializer\Expose
     * @var string
     */
    protected $domain;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255,nullable=false)
     * @Serializer\Expose
     */
    protected $firstname; // e.g.: Jill

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255,nullable=false)
     * @Serializer\Expose
     */
    protected $surname; // e.g.: Jones

    /**
     * Timestamp of when the entry sent to the notification endpoints.
     *
     * @var \DateTime $notified
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $notified;

    /**
     * @param string $domain
     *
     * @return self
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $firstname
     *
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param \DateTime $notified
     *
     * @return self
     */
    public function setNotified(\DateTime $notified)
    {
        $this->notified = $notified;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNotified()
    {
        return $this->notified;
    }

    /**
     * @param string $surname
     *
     * @return self
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
}
