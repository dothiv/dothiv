<?php

namespace Dothiv\APIBundle\Controller;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
            return $user; // TODO use the form to serialize the user object
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
        throw new RuntimeException();
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
        throw new RuntimeException();
    }

    /**
     * @ApiDoc(
     *   section="security",
     *   resource=false,
     *   description="Start Facebook login process"
     * )
     */
    public function facebookLoginStartAction() {
        $facebook = $this->get('facebook');
        $loginUrl = $facebook->getLoginUrl(
           array(
                'redirect_uri' => $this->generateUrl('dothiv_api_security_facebook_login_return', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'scope' => 'email',
                'display' => 'popup'
        ));
        return $this->redirect($loginUrl);
    }

    /**
     * @ApiDoc(
     *   section="security",
     *   resource=false,
     *   description="Return point for facebook login"
     * )
     */
    public function facebookLoginReturnAction() {
        // handled by Symfony firewall
        throw new RuntimeException();
    }

    /**
     * We need to define this explicitly, as this is not a FOSRestController.
     * Allowed methods are GET, POST, DELETE and OPTIONS 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function optionsLoginAction() {
        $response = new Response();
        $response->headers->set('Allow', 'GET, POST, DELETE, OPTIONS');
        return $response;
    }

}
