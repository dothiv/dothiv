<?php

namespace DotHiv\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
     *   resource=true,
     *   description="Get current user object, or {} if not logged in.",
     *   statusCodes={
     *     200="Success",
     *   }
     * )
     */
    public function getLoginAction() {
        $user = $this->getUser();
        if ($user) {
            return $user;
        } else {
            return new Response("{}"); // TODO This is json-only. Respect the client's Accept-header.
        }
    }

    /**
     * @ApiDoc(
     *   section="security",
     *   resource=true,
     *   description="Log in.",
     *   statusCodes={
     *     201="Login successful",
     *   }
     * )
     */
    public function postLoginAction() {
        // handled by Symfony firewall
    }

    /**
     * @ApiDoc(
     *   section="security",
     *   resource=false,
     *   description="Log out",
     *   statusCodes={
     *     200="Success",
     *   }
     * )
     */
    public function deleteLoginAction() {
        // handled by Symfony firewall
    }

}