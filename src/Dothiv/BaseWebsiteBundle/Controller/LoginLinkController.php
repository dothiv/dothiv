<?php

namespace Dothiv\BaseWebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * This controller is used to redirect login links from login emails to the app links
 * (which contain a hashbang and can break when forwarding).
 *
 * In the future we might change the app links too, and so we have means to keep login links
 * working (which may be valid up to two weeks).
 *
 * See https://trello.com/c/XpIRdn4O
 */
class LoginLinkController
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $app_route
     * @param string $locale
     * @param string $identifier
     * @param string $token
     *
     * @return Response
     */
    public function loginLinkAction($app_route, $locale, $identifier, $token)
    {
        $response = new Response();
        $response->setStatusCode(301);
        $urlToApp = $this->router->generate($app_route, array('locale' => $locale), RouterInterface::ABSOLUTE_URL);
        $response->headers->set(
            'Location',
            $urlToApp . sprintf('#!/auth/%s/%s', $identifier, $token)
        );
        return $response;
    }
}
