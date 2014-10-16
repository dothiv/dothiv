<?php


namespace Dothiv\CharityWebsiteBundle\Entity;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\Traits\CreateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Stores notifications for domain configurations.
 *
 * @ORM\Entity(repositoryClass="Dothiv\CharityWebsiteBundle\Repository\DomainConfigurationNotificationRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class DomainConfigurationNotification extends Entity
{
    use CreateTime;

    /**
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\Domain")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Domain
     */
    protected $domain;

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
} 
