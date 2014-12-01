<?php

namespace Dothiv\DomainGiftBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class PageController extends \Dothiv\BaseWebsiteBundle\Controller\PageController
{
    public function domainGiftAction(Request $request, $domain)
    {
        return parent::pageAction($request, null, preg_replace('/\.hiv$/i', '', $domain), null, ':page', 'domainGift');
    }
}
