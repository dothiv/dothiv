<?php

namespace Dothiv\BusinessBundle\Listener;

use Dothiv\BusinessBundle\Event\DomainEvent;

/**
 * Notifies a podio app for each new domain registration
 */
class NotifyPodioRegistrationListener
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param DomainEvent $event
     */
    public function onDomainRegistered(DomainEvent $event)
    {
        $domain = $event->getDomain();
        $fields = array(
            'title'       => $domain->getName(),
            'whois-email' => $domain->getOwnerEmail()
        );
        if ($domain->getRegistrar()->canSendRegistrationNotification()) {
            $fields['status'] = 1;
        }
        try {
            \Podio::setup($this->config['clientId'], $this->config['clientSecret']);
            \Podio::authenticate_with_app($this->config['appId'], $this->config['appToken']);
            \PodioItem::create($this->config['appId'], array(
                'fields' => $fields
            ));
        } catch (\PodioError $e) {
            // TODO: Log.
        }
    }
} 
