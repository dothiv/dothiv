<?php

namespace Dothiv\QLPPartnerBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LandingPageController extends PageController
{
    /**
     * @param Request $request
     * @param string  $partner
     *
     * @return Response
     * @throws NotFoundHttpException If partner entry not found.
     */
    public function partnerAction(Request $request, $partner)
    {
        try {
            $entry = $this->getContent()->buildEntry('QLP', $partner, 'en');
        } catch (InvalidArgumentException $e) {
            throw new NotFoundHttpException("$partner not found.");
        }
        return parent::pageAction($request, 'en', $partner, null, 'Page:landingpage', 'QLP');
    }
}
