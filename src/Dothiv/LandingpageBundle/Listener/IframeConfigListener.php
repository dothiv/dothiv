<?php

namespace Dothiv\LandingpageBundle\Listener;

use Dothiv\BaseWebsiteBundle\Contentful\ContentInterface;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface;
use Dothiv\LandingpageBundle\Service\GenitivfyServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

/**
 * This adds landing page configuration settings to the iFrame config
 *
 * @see Dothiv\BusinessBundle\Listener\ClickCounterIframeConfigListener
 */
class IframeConfigListener
{
    /**
     * @param LandingpageConfigurationRepositoryInterface $configRepo
     * @param ContentInterface                            $content
     * @param array                                       $clickCounterConfig See Dothiv\BusinessBundle\DependencyInjection\Configuration
     * @param GenitivfyServiceInterface                   $genitivfy
     */
    public function __construct(
        LandingpageConfigurationRepositoryInterface $configRepo,
        ContentInterface $content,
        array $clickCounterConfig,
        GenitivfyServiceInterface $genitivfy
    )
    {
        $this->configRepo = $configRepo;
        $this->content    = $content;
        $this->locales    = $clickCounterConfig['locales'];
        $this->genitivfy  = $genitivfy;
        $this->parsedown  = new \Parsedown();
        $this->parsedown->setBreaksEnabled(false);

    }

    /**
     * @param ClickCounterConfigurationEvent $event
     */
    public function onClickCounterConfiguration(ClickCounterConfigurationEvent $event)
    {
        $iframeConfig   = $event->getConfig();
        $domain         = $event->getDomain();
        $configOptional = $this->configRepo->findByDomain($domain);
        if ($configOptional->isEmpty()) {
            return;
        }
        /** @var LandingpageConfiguration $config */
        $config                      = $configOptional->get();
        $iframeConfig['landingPage'] = [
            'defaultLocale' => $config->getLanguage()->toScalar()
        ];
        foreach ($this->locales as $locale) {
            $replace                                         = [
                '%%firstname%%' => $this->genitivfy->genitivfy($config->getName(), new IdentValue($locale)),
                '%%domain%%'    => HivDomainValue::create($domain->getName())->toUTF8()
            ];
            $iframeConfig['landingPage']['strings'][$locale] = array(
                'title'           => $this->getString('title', $locale, $replace),
                'about'           => $this->getString('about', $locale, $replace),
                'learnMore'       => $this->getString('learnMore', $locale, $replace),
                'getYourOwn'      => $this->getString('getYourOwn', $locale, $replace),
                'tellYourFriends' => $this->getString('tellYourFriends', $locale, $replace),
                'tweet'           => $this->getString('tweet', $locale, $replace),
                'imprint'         => $this->getString('imprint', $locale, $replace)
            );
        }
        $event->setConfig($iframeConfig);
    }

    protected function getString($code, $locale, array $replace)
    {
        $v = $this->content->buildEntry('String', $code, $locale)->value;
        $v = strip_tags($this->parsedown->text($v), '<a><em><strong><code>');
        $v = str_replace(array_keys($replace), array_values($replace), $v);
        return $v;
    }
}
