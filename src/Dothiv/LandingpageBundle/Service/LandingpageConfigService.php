<?php

namespace Dothiv\LandingpageBundle\Service;

use Dothiv\BaseWebsiteBundle\Contentful\ContentInterface;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface;
use Dothiv\LandingpageBundle\Service\GenitivfyServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

class LandingpageConfigService implements LandingpageConfigServiceInterface
{
    /**
     * @param ContentInterface          $content
     * @param array                     $clickCounterConfig See Dothiv\BusinessBundle\DependencyInjection\Configuration
     * @param GenitivfyServiceInterface $genitivfy
     */
    public function __construct(
        ContentInterface $content,
        array $clickCounterConfig,
        GenitivfyServiceInterface $genitivfy
    )
    {
        $this->content   = $content;
        $this->locales   = $clickCounterConfig['locales'];
        $this->genitivfy = $genitivfy;
        $this->parsedown = new \Parsedown();
        $this->parsedown->setBreaksEnabled(false);

    }

    /**
     * {@inheritdoc}
     */
    public function buildConfig(LandingpageConfiguration $config)
    {
        $landingpageConfig = [
            'defaultLocale' => $config->getLanguage()->toScalar()
        ];
        foreach ($this->locales as $locale) {
            $replace                               = [
                '%%firstname%%' => $this->genitivfy->genitivfy($config->getName(), new IdentValue($locale)),
                '%%domain%%'    => HivDomainValue::create($config->getDomain()->getName())->toUTF8()
            ];
            $landingpageConfig['strings'][$locale] = array(
                'title'           => $this->getString('title', $locale, $replace),
                'about'           => $this->getString('about', $locale, $replace),
                'learnMore'       => $this->getString('learnMore', $locale, $replace),
                'getYourOwn'      => $this->getString('getYourOwn', $locale, $replace),
                'tellYourFriends' => $this->getString('tellYourFriends', $locale, $replace),
                'tweet'           => $this->getString('tweet', $locale, $replace),
                'imprint'         => $this->getString('imprint', $locale, $replace)
            );
        }
        return $landingpageConfig;
    }

    protected function getString($code, $locale, array $replace)
    {
        $v = $this->content->buildEntry('String', $code, $locale)->value;
        $v = strip_tags($this->parsedown->text($v), '<a><em><strong><code>');
        $v = str_replace(array_keys($replace), array_values($replace), $v);
        return $v;
    }
}
