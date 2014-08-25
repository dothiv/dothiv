<?php

namespace Dothiv\PremiumConfiguratorBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

class PremiumBannerConfigPutRequest extends PremiumBannerConfigGetRequest
{
    /**
     * @var string
     * @Assert\Regex("/^[a-f0-9]+$/")
     */
    public $visual; // e.g.: attachm3nt

    /**
     * @var string
     * @Assert\Regex("/^[a-f0-9]+$/")
     */
    public $bg; // e.g.: attachm3nt

    /**
     * @var string
     * @Assert\Regex("/^[a-f0-9]+$/")
     */
    public $extrasVisual; // e.g.: attachm3nt

    /**
     * @var string
     * @Assert\Regex("/^#([a-fA-F0-9]{3}){1,2}$/")
     */
    public $fontColor; // e.g.: #333

    /**
     * @var string
     * @Assert\Regex("/^#([a-fA-F0-9]{3}){1,2}$/")
     */
    public $bgColor; // e.g.: #333

    /**
     * @var string
     * @Assert\Regex("/^#([a-fA-F0-9]{3}){1,2}$/")
     */
    public $barColor; // e.g.: #333

    /**
     * @var string
     * @Assert\Regex("/^(https*:)*\/\/.+/")
     */
    public $extrasLinkUrl;

    /**
     * @var string
     * @Assert\Length(max=255)
     */
    public $extrasHeadline;

    /**
     * @var string
     * @Assert\Length(max=255)
     */
    public $extrasLinkLabel;

    /**
     * @var string
     */
    public $extrasText;

    /**
     * @var string
     * @Assert\Length(max=255)
     */
    public $headlineFont;

    /**
     * @var string
     * @Assert\Length(max=255)
     */
    public $headlineFontWeight;

    /**
     * @var integer
     * @Assert\Range(min=8,max=30)
     */
    public $headlineFontSize;

    /**
     * @var string
     * @Assert\Length(max=255)
     */
    public $textFont;

    /**
     * @var string
     * @Assert\Length(max=255)
     */
    public $textFontWeight;

    /**
     * @var integer
     * @Assert\Range(min=8,max=30)
     */
    public $textFontSize;
}
