<?php

namespace Dothiv\PremiumConfiguratorBundle\Entity;

use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Traits\CreateUpdateTime;
use Dothiv\BusinessBundle\ValueObject\HexValue;
use Dothiv\BusinessBundle\ValueObject\URLValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Premium banner configuration.
 *
 * @ORM\Entity(repositoryClass="Dothiv\PremiumConfiguratorBundle\Repository\PremiumBannerRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class PremiumBanner extends Entity
{
    use CreateUpdateTime;

    /**
     * @ORM\OneToOne(targetEntity="Dothiv\BusinessBundle\Entity\Attachment")
     * @ORM\JoinColumn(onDelete="RESTRICT",nullable=true)
     * @Assert\Type("Dothiv\BusinessBundle\Entity\Attachment")
     * @var Attachment
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getVisualSerialized")
     */
    protected $visual;

    /**
     * @ORM\OneToOne(targetEntity="Dothiv\BusinessBundle\Entity\Attachment")
     * @ORM\JoinColumn(onDelete="RESTRICT",nullable=true)
     * @Assert\Type("Dothiv\BusinessBundle\Entity\Attachment")
     * @var Attachment
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getBgSerialized")
     */
    protected $bg;

    /**
     * @ORM\OneToOne(targetEntity="Dothiv\BusinessBundle\Entity\Attachment")
     * @ORM\JoinColumn(onDelete="RESTRICT",nullable=true)
     * @Assert\Type("Dothiv\BusinessBundle\Entity\Attachment")
     * @var Attachment
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getExtrasVisualSerialized")
     */
    protected $extrasVisual;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\RegEx("/^#[A-F0-9]{6}$/")
     * @var HexValue
     * @Serializer\Expose
     */
    protected $fontColor;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\RegEx("/^#[A-F0-9]{6}$/")
     * @var HexValue
     * @Serializer\Expose
     */
    protected $bgColor;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\RegEx("/^#[A-F0-9]{6}$/")
     * @var HexValue
     * @Serializer\Expose
     */
    protected $barColor;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(max=255)
     * @var string
     * @Serializer\Expose
     */
    protected $headlineFont;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(max=255)
     * @var string
     * @Serializer\Expose
     */
    protected $headlineFontStyle;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(max=255)
     * @var string
     * @Serializer\Expose
     */
    protected $textFont;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(max=255)
     * @var string
     * @Serializer\Expose
     */
    protected $textFontStyle;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(max=255)
     * @var string
     * @Serializer\Expose
     */
    protected $extrasHeadline;

    /**
     * @ORM\Column(type="text",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $extrasText;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(max=255)
     * @var URLValue
     * @Serializer\Expose
     */
    protected $extrasLinkUrl;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(max=255)
     * @var string
     * @Serializer\Expose
     */
    protected $extrasLinkLabel;

    /**
     * The regular banner
     *
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\Banner")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Banner
     */
    protected $banner;

    /**
     * @Serializer\Expose
     * @Serializer\SerializedName("@context")
     * @var array
     */
    protected $context;

    /**
     * @param Banner $banner
     */
    public function setBanner(Banner $banner)
    {
        $this->banner = $banner;
    }

    /**
     * @return Banner
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @param HexValue|null $barColor
     */
    public function setBarColor(HexValue $barColor = null)
    {
        $this->barColor = $barColor == null ? null : (string)$barColor;
    }

    /**
     * @return HexValue|null
     */
    public function getBarColor()
    {
        return $this->barColor == null ? null : new HexValue($this->barColor);
    }

    /**
     * @param Attachment|null $bg
     */
    public function setBg(Attachment $bg = null)
    {
        $this->bg = $bg;
    }

    /**
     * @return Attachment|null
     */
    public function getBg()
    {
        return $this->bg;
    }

    /**
     * @param HexValue|null $bgColor
     */
    public function setBgColor(HexValue $bgColor = null)
    {
        $this->bgColor = $bgColor == null ? null : (string)$bgColor;
    }

    /**
     * @return HexValue|null
     */
    public function getBgColor()
    {
        return $this->bgColor == null ? null : new HexValue($this->bgColor);
    }

    /**
     * @param string|null $extrasHeadline
     */
    public function setExtrasHeadline($extrasHeadline = null)
    {
        $this->extrasHeadline = $extrasHeadline;
    }

    /**
     * @return string|null
     */
    public function getExtrasHeadline()
    {
        return $this->extrasHeadline;
    }

    /**
     * @param string|null $extrasLinkLabel
     */
    public function setExtrasLinkLabel($extrasLinkLabel = null)
    {
        $this->extrasLinkLabel = $extrasLinkLabel;
    }

    /**
     * @return string|null
     */
    public function getExtrasLinkLabel()
    {
        return $this->extrasLinkLabel;
    }

    /**
     * @param URLValue|null $extrasLinkUrl
     */
    public function setExtrasLinkUrl(URLValue $extrasLinkUrl = null)
    {
        $this->extrasLinkUrl = $extrasLinkUrl == null ? null : (string)$extrasLinkUrl;
    }

    /**
     * @return URLValue|null
     */
    public function getExtrasLinkUrl()
    {
        return $this->extrasLinkUrl == null ? null : new URLValue($this->extrasLinkUrl);
    }

    /**
     * @param string|null $extrasText
     */
    public function setExtrasText($extrasText = null)
    {
        $this->extrasText = $extrasText;
    }

    /**
     * @return string|null
     */
    public function getExtrasText()
    {
        return $this->extrasText;
    }

    /**
     * @param Attachment|null $extrasVisual
     */
    public function setExtrasVisual(Attachment $extrasVisual = null)
    {
        $this->extrasVisual = $extrasVisual;
    }

    /**
     * @return Attachment
     */
    public function getExtrasVisual()
    {
        return $this->extrasVisual;
    }

    /**
     * @param HexValue|null $fontColor
     */
    public function setFontColor(HexValue $fontColor = null)
    {
        $this->fontColor = $fontColor == null ? null : (string)$fontColor;
    }

    /**
     * @return HexValue|null
     */
    public function getFontColor()
    {
        return $this->fontColor == null ? $this->fontColor : new HexValue($this->fontColor);
    }

    /**
     * @param string|null $headlineFont
     */
    public function setHeadlineFont($headlineFont = null)
    {
        $this->headlineFont = $headlineFont;
    }

    /**
     * @return string|null
     */
    public function getHeadlineFont()
    {
        return $this->headlineFont;
    }

    /**
     * @param string|null $headlineFontStyle
     */
    public function setHeadlineFontStyle($headlineFontStyle = null)
    {
        $this->headlineFontStyle = $headlineFontStyle;
    }

    /**
     * @return string|null
     */
    public function getHeadlineFontStyle()
    {
        return $this->headlineFontStyle;
    }

    /**
     * @param string|null $textFont
     */
    public function setTextFont($textFont = null)
    {
        $this->textFont = $textFont;
    }

    /**
     * @return string|null
     */
    public function getTextFont()
    {
        return $this->textFont;
    }

    /**
     * @param string|null $textFontStyle
     */
    public function setTextFontStyle($textFontStyle = null)
    {
        $this->textFontStyle = $textFontStyle;
    }

    /**
     * @return string|null
     */
    public function getTextFontStyle()
    {
        return $this->textFontStyle;
    }

    /**
     * @param Attachment|null $visual
     */
    public function setVisual(Attachment $visual = null)
    {
        $this->visual = $visual;
    }

    /**
     * @return Attachment|null
     */
    public function getVisual()
    {
        return $this->visual;
    }

    /**
     * @return null|string
     */
    public function getVisualSerialized()
    {
        return $this->visual == null ? null : $this->visual->getHandle();
    }

    /**
     * @return null|string
     */
    public function getBgSerialized()
    {
        return $this->bg == null ? null : $this->bg->getHandle();
    }

    /**
     * @return null|string
     */
    public function getExtrasVisualSerialized()
    {
        return $this->extrasVisual == null ? null : $this->extrasVisual->getHandle();
    }

    /**
     * @param array $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
