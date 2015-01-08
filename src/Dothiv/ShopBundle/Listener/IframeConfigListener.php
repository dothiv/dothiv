<?php

namespace Dothiv\ShopBundle\Listener;

use Dothiv\BaseWebsiteBundle\Contentful\ContentInterface;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Repository\OrderRepositoryInterface;
use Dothiv\ValueObject\HivDomainValue;

/**
 * This adds landing page configuration settings to the iFrame config
 *
 * @see Dothiv\BusinessBundle\Listener\ClickCounterIframeConfigListener
 */
class IframeConfigListener
{
    /**
     * @param OrderRepositoryInterface $orderRepo
     * @param ContentInterface         $content
     * @param array                    $clickCounterConfig See Dothiv\BusinessBundle\DependencyInjection\Configuration
     */
    public function __construct(OrderRepositoryInterface $orderRepo, ContentInterface $content, array $clickCounterConfig)
    {
        $this->orderRepo = $orderRepo;
        $this->content   = $content;
        $this->locales   = $clickCounterConfig['locales'];
        $this->parsedown = new \Parsedown();
        $this->parsedown->setBreaksEnabled(false);
    }

    /**
     * @param ClickCounterConfigurationEvent $event
     */
    public function onClickCounterConfiguration(ClickCounterConfigurationEvent $event)
    {
        $iframeConfig  = $event->getConfig();
        $domain        = $event->getDomain();
        $orderOptional = $this->orderRepo->findLatestByDomain(new HivDomainValue($domain->getName()));
        if ($orderOptional->isDefined()) {
            /** @var Order $order */
            $order                       = $orderOptional->get();
            $locale                      = $order->getLanguage()->toScalar();
            $iframeConfig['landingPage'] = [
                'defaultLocale' => $locale
            ];
            $replace                     = [
                '%%firstname%%' => $domain->getOwner()->getFirstname(),
                '%%domain%%'    => HivDomainValue::create($domain->getName())->toUTF8()
            ];
            if ($order->getGift()) {
                $replace['%%firstname%%'] = $order->getPresenteeFirstname()->get();
            }
            foreach ($this->locales as $locale) {
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
