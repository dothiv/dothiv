<?php

namespace Dothiv\HivDomainStatusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The check result of a hiv domain
 *
 * @ORM\Entity(repositoryClass="Dothiv\HivDomainStatusBundle\Repository\HivDomainCheckRepository")
 * @ORM\Table(indexes={@ORM\Index(name="hivdomaincheck__created_idx", columns={"created"})})
 * @Serializer\ExclusionPolicy("all")
 */
class HivDomainCheck extends Entity
{
    use Traits\CreateTime;

    /**
     * The domain
     *
     * @ORM\ManyToOne(targetEntity="\Dothiv\BusinessBundle\Entity\Domain")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Assert\NotNull
     * @Assert\Type("Dothiv\BusinessBundle\Entity\Domain")
     * @var Domain
     */
    protected $domain;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("bool")
     * @Assert\NotNull
     * @var bool
     */
    protected $dnsOk = false;

    /**
     * @ORM\Column(type="json_array", nullable=false)
     * @Assert\Type("array")
     * @Assert\NotNull
     * @var string[]
     */
    protected $addresses = array();

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\Type("string")
     * @Assert\NotNull
     * @var string
     */
    protected $url;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\Type("integer")
     * @Assert\NotNull
     * @var int
     */
    protected $statusCode = 0;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("bool")
     * @Assert\NotNull
     * @var bool
     */
    protected $scriptPresent = false;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("bool")
     * @Assert\NotNull
     * @var bool
     */
    protected $iframePresent = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type("string")
     * @var string
     */
    protected $iframeTarget = null;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("bool")
     * @Assert\NotNull
     * @var bool
     */
    protected $iframeTargetOk = false;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("bool")
     * @Assert\NotNull
     * @var bool
     */
    protected $valid = false;

    /**
     * @return string[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param array $addresses
     *
     * @return self
     */
    public function setAddresses(array $addresses)
    {
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getDnsOk()
    {
        return $this->dnsOk;
    }

    /**
     * @param boolean $dnsOk
     *
     * @return self
     */
    public function setDnsOk($dnsOk)
    {
        $this->dnsOk = (bool)$dnsOk;
        return $this;
    }

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
     * @return boolean
     */
    public function getIframePresent()
    {
        return $this->iframePresent;
    }

    /**
     * @param boolean $iframePresent
     *
     * @return self
     */
    public function setIframePresent($iframePresent)
    {
        $this->iframePresent = (bool)$iframePresent;
        return $this;
    }

    /**
     * @return string
     */
    public function getIframeTarget()
    {
        return $this->iframeTarget;
    }

    /**
     * @param string $iframeTarget
     *
     * @return self
     */
    public function setIframeTarget($iframeTarget)
    {
        $this->iframeTarget = $iframeTarget;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIframeTargetOk()
    {
        return $this->iframeTargetOk;
    }

    /**
     * @param boolean $iframeTargetOk
     *
     * @return self
     */
    public function setIframeTargetOk($iframeTargetOk)
    {
        $this->iframeTargetOk = (boolean)$iframeTargetOk;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getScriptPresent()
    {
        return $this->scriptPresent;
    }

    /**
     * @param boolean $scriptPresent
     *
     * @return self
     */
    public function setScriptPresent($scriptPresent)
    {
        $this->scriptPresent = (boolean)$scriptPresent;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (int)$statusCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param boolean $valid
     *
     * @return self
     */
    public function setValid($valid)
    {
        $this->valid = (boolean)$valid;
        return $this;
    }
}
