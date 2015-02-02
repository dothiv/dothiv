<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\W3CDateTimeValue;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * WHOIS Creation Date
     *
     * @var W3CDateTimeValue|null
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    protected $creationDate;

    /**
     * WHOIS Registry Expiry Date
     *
     * @var W3CDateTimeValue|null
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    protected $expiryDate;

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
        $ns        = $whois->get('Name Server');
        if ($ns) {
            $w->whois['Name Server'] = $whois->get('Name Server')->toArray();
        }
        $w->expiryDate   = new \DateTime($whois->get('Registry Expiry Date'));
        $w->creationDate = new \DateTime($whois->get('Creation Date'));
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

    /**
     * {@inheritdoc}
     */
    public function getPublicId()
    {
        return $this->getDomain()->toScalar();
    }

    /**
     * @return W3CDateTimeValue
     */
    public function getExpiryDate()
    {
        return new W3CDateTimeValue($this->expiryDate);
    }

    /**
     * @return W3CDateTimeValue
     */
    public function getCreationDate()
    {
        return new W3CDateTimeValue($this->creationDate);
    }
}
