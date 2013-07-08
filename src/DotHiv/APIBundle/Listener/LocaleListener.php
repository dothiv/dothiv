<?php

namespace DotHiv\APIBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Sets the user's locale by the Language-Accept header the user sends.
 * If there's a locale set in the user's session, this value will *not*
 * be replaced.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class LocaleListener extends \Symfony\Component\HttpKernel\EventListener\LocaleListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $req = $event->getRequest();
        $locale = $req->getPreferredLanguage();
        $req->setLocale($req->getSession()->get('locale', $locale));
    }
}
