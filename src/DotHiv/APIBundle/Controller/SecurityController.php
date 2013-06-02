<?php

namespace DotHiv\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * This controller manages security features of the API,
 * unless they are provided by the FOSRestBundle.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class SecurityController extends Controller {

    /**
     * Allows the client to distinguish whether it is 
     * logged in or not.
     * 
     * @ApiDoc(
     *   section="security",
     *   resource=false,
     *   description="Check current login state.",
     *   statusCodes={
     *     200="Returned when logged in",
     *     400="Retruned when not logged in",
     *   }
     * )
     */
    public function checkLoginStateAction() {
        $resp = new Response();
        $resp->setStatusCode(false === $this->get('security.context')->isGranted('ROLE_USER') ? 400 : 200);
        return $resp;
    }

}