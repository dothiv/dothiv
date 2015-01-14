<?php


namespace Dothiv\CharityWebsiteBundle\Entity;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\Traits\CreateTime;
use Doctrine\ORM\Mapping as ORM;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Stores notifications for domain configurations.
 *
 * @ORM\Entity(repositoryClass="Dothiv\CharityWebsiteBundle\Repository\DomainNotificationRepository")
 * @ORM\Table(
 *  indexes={
 *      @ORM\Index(name="domain_notification__type_idx", columns={"type"})
 *  }
 * )
 * @Serializer\ExclusionPolicy("all")
 */
class DomainNotification extends Entity
{
    use CreateTime;

    /**
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\Domain")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Domain
     */
    protected $domain;

    /**
     * Type of the notification
     *
     * @ORM\Column(type="string",nullable=false)
     * @Assert\NotBlank
     * @Assert\NotNull
     * @var string
     */
    protected $type;

    /**
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     *
     * @return self
     */
    public function setDomain(Domain $domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return IdentValue
     */
    public function getType()
    {
        return new IdentValue($this->type);
    }

    /**
     * @param IdentValue $type
     *
     * @return self
     */
    public function setType(IdentValue $type)
    {
        $this->type = $type->toScalar();
        return $this;
    }
}
