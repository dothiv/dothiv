<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use LogicException;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * The dotHIV banner to be shown on websites
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\BannerRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Benedikt Budig <bb@dothiv.org>
 * @author Markus Tacker <m@click4life.hiv>
 */
class Banner extends Entity
{
    use Traits\CreateUpdateTime;

    /**
     * Domain to redirect to, if desired
     *
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Regex("/^(https*:)*\/\/.+/")
     * @Serializer\Expose
     * @Serializer\SerializedName("redirect_url")
     */
    protected $redirectUrl;

    /**
     * Language of banner texts
     *
     * @ORM\Column(type="string",nullable=true)
     * @Serializer\Expose
     */
    protected $language;

    /**
     * Position for banner placement on website
     *
     * @ORM\Column(type="string",nullable=true)
     * @Serializer\Expose
     * @Serializer\SerializedName("position_first")
     */
    protected $position;

    /**
     * Alternative position for banner placement on website, used for returning
     * visitors
     *
     * @ORM\Column(type="string",nullable=true)
     * @Serializer\Expose
     * @Serializer\SerializedName("position")
     */
    protected $positionAlternative;

    /**
     * The domain that displays this banner
     *
     * @ORM\ManyToOne(targetEntity="Domain",inversedBy="banners")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Domain
     */
    protected $domain;

    /**
     * Returns the FQDN of the redirect domain.
     *
     * @return string FQDN for redirect
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Sets the FQDN of the redirect domain.
     *
     * @param string $redirectUrl FQDN for redirect
     */
    public function setRedirectUrl($redirectUrl)
    {
        $redirectUrl = trim($redirectUrl);
        $this->redirectUrl = empty($redirectUrl) ? null : $redirectUrl;
    }

    /**
     * Returns the banner language.
     *
     * @return string language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the banner language.
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Returns the display position.
     *
     * @return string display position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the display position.
     *
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Returns the alternative display position.
     *
     * @return string alternative display position
     */
    public function getPositionAlternative()
    {
        return $this->positionAlternative;
    }

    /**
     * Sets the alternative display position.
     *
     * @param string $positionAlternative alternative display position
     */
    public function setPositionAlternative($positionAlternative)
    {
        $this->positionAlternative = $positionAlternative;
    }

    /**
     * Return the domain that displays this banner.
     *
     * @return Domain domain that displays the banner
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the domain of this banner (if previously not assigned),
     * transfers the banner to new domain (if previously assigned),
     * removes any possible domain association (if called with 'NULL').
     *
     * @param Domain $newDomain
     */
    public function setDomain($newDomain = null)
    {
        // remove this banner from current domain's list, if any
        if ($this->domain !== null) {
            $this->domain->getBanners()->removeElement($this);
            if ($this->domain->getActiveBanner() === $this)
                $this->domain->setActiveBanner(null);
        }
        // set new domain
        $this->domain = $newDomain;

        // add this domain to new domain's banners, if new domain exists
        if ($newDomain !== null)
            $newDomain->getBanners()->add($this);
    }

    /**
     * Activates this banner for the associated domain. The previously
     * activated banner will be deactivated (if any).
     */
    public function activate()
    {
        if ($this->domain === null)
            throw new LogicException('Banner cannot be activated with no domain associated');
        $this->domain->setActiveBanner($this);
    }
}
