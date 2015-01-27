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
 * This entity tracks deleted domains.
 *
 * The domain entity has a unique key on the name, so we need to
 * track deleted domains in a separate table (there can also be multiple entries)
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\DeletedDomainRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class DeletedDomain extends Entity
{
    use Traits\CreateTime;

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
     * @param HivDomainValue $domain
     */
    public function __construct(HivDomainValue $domain)
    {
        $this->domain = $domain->toScalar();
    }

    /**
     * @return HivDomainValue
     */
    public function getDomain()
    {
        return new HivDomainValue($this->domain);
    }
}
