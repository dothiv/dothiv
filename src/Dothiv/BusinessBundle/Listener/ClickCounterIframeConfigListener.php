<?php

namespace Dothiv\BusinessBundle\Listener;

use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\ValueObject\URLValue;
use Guzzle\Http\Client;

/**
 * This listener updates the iframe configuration on the CNAME instance.
 */
class ClickCounterIframeConfigListener
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var URLValue
     */
    private $serviceUrl;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @param Client $client
     * @param string $password
     * @param string $serviceUrl
     * @param string $username
     */
    public function __construct(Client $client, $serviceUrl, $username, $password)
    {
        $this->client     = $client;
        $this->serviceUrl = new URLValue($serviceUrl);
        $this->username   = $username;
        $this->password   = $password;
    }

    /**
     * @param ClickCounterConfigurationEvent $event
     */
    public function onClickCounterConfiguration(ClickCounterConfigurationEvent $event)
    {
        $domain = $event->getDomain();
        $config = $event->getConfig();

        $iframeConfig = [];
        if (isset($config['redirect_url'])) {
            $iframeConfig['redirect'] = $config['redirect_url'];
        }

        $iframeConfig = $event->getDispatcher()->dispatch(
            BusinessEvents::CLICKCOUNTER_IFRAME_CONFIGURATION, new ClickCounterConfigurationEvent($domain, $iframeConfig)
        )->getConfig();

        $this->client->put(
            $this->serviceUrl->toScalar() . 'domain/' . $domain->getName(),
            array('Content-Type' => 'application/json'),
            json_encode($iframeConfig, JSON_UNESCAPED_SLASHES),
            array(
                'auth' => array($this->username, $this->password)
            )
        )->send();
    }
}
