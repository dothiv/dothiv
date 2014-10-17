<?php


namespace Dothiv\PremiumConfiguratorBundle\Service;

use Dothiv\BusinessBundle\Service\LinkableAttachmentStoreInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use PhpOption\Option;

class PremiumClickCounterConfigurationDecorator implements PremiumClickCounterConfigurationDecoratorInterface
{

    /**
     * @var LinkableAttachmentStoreInterface
     */
    private $attachmentStore;

    public function __construct(LinkableAttachmentStoreInterface $attachmentStore)
    {
        $this->attachmentStore = $attachmentStore;
    }

    /**
     * Decorate the configuration in $config with premium settings
     *
     * @param array         $config
     * @param PremiumBanner $premiumBanner
     *
     * @return array
     */
    public function decorate($config, PremiumBanner $premiumBanner)
    {
        $config['premium'] = true;
        if (Option::fromValue($premiumBanner->getVisual())->isDefined()) {
            $config['visual']       = (string)$this->attachmentStore->getUrl($premiumBanner->getVisual(), 'image/*;scale=visual');
            $config['visual@micro'] = (string)$this->attachmentStore->getUrl($premiumBanner->getVisual(), 'image/*;scale=visual-micro');
        }
        if (Option::fromValue($premiumBanner->getBg())->isDefined()) {
            $config['bg'] = (string)$this->attachmentStore->getUrl($premiumBanner->getBg(), 'image/*;scale=bg');
        }
        if (Option::fromValue($premiumBanner->getBarColor())->isDefined()) {
            $config['barColor'] = (string)$premiumBanner->getBarColor();
        }
        if (Option::fromValue($premiumBanner->getBgColor())->isDefined()) {
            $config['bgColor'] = (string)$premiumBanner->getBgColor();
        }
        if (Option::fromValue($premiumBanner->getFontColor())->isDefined()) {
            $config['fontColor'] = (string)$premiumBanner->getFontColor();
        }
        if (Option::fromValue($premiumBanner->getHeadlineFont())->isDefined()) {
            $config['headlineFont']       = $premiumBanner->getHeadlineFont();
            $config['headlineFontWeight'] = $premiumBanner->getHeadlineFontWeight();
            $config['headlineFontSize']   = $premiumBanner->getHeadlineFontSize();
        }
        if (Option::fromValue($premiumBanner->getTextFont())->isDefined()) {
            $config['textFont']       = $premiumBanner->getTextFont();
            $config['textFontWeight'] = $premiumBanner->getTextFontWeight();
            $config['textFontSize']   = $premiumBanner->getTextFontSize();
        }
        return $config;
    }
} 
