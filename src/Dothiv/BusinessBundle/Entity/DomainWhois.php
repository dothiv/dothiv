<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dothiv\ValueObject\HivDomainValue;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;

/**
 * WHOIS entry for a domain
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\DomainWhoisRepository")
 * @AssertORM\UniqueEntity("domain")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="domainwhois__domain",columns={"domain"})})
 * @Serializer\ExclusionPolicy("all")
 */
class DomainWhois extends Entity
{
    use Traits\CreateUpdateTime;

    /**
     * Domain name
     *
     * @ORM\Column(type="string", nullable=false)
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $domain;

    /**
     * WHOIS
     *
     * @var array
     * @ORM\Column(type="json_array", nullable=false)
     * @Assert\Type("array")
     * @Assert\NotNull()
     */
    protected $whois;

    /**
     * @param HivDomainValue  $domain
     * @param ArrayCollection $whois
     *
     * @return self
     */
    public static function create(HivDomainValue $domain, ArrayCollection $whois)
    {
        $w         = new DomainWhois();
        $w->domain = $domain->toScalar();
        $w->whois  = $whois->toArray();
        return $w;
    }

    /**
     * @return HivDomainValue
     */
    public function getDomain()
    {
        return new HivDomainValue($this->domain);
    }

    /**
     * @return ArrayCollection
     */
    public function getWhois()
    {
        return new ArrayCollection($this->whois);
    }

    /**
     * @param DomainWhois $whois
     *
     * @return self
     */
    public function update(DomainWhois $whois)
    {
        $this->whois = $whois->getWhois()->toArray();
        return $this;
    }
}
