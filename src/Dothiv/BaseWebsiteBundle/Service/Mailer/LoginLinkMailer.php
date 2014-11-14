<?php

namespace Dothiv\BaseWebsiteBundle\Service\Mailer;

use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Event\UserTokenEvent;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use PhpOption\Option;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class LoginLinkMailer
{

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $host;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var ContentMailerInterface
     */
    private $contentMailer;

    /**
     * @param ContentMailerInterface $contentMailer
     * @param RouterInterface        $router
     * @param string                 $route
     * @param string                 $host
     * @param UserServiceInterface   $userService
     */
    public function __construct(
        ContentMailerInterface $contentMailer,
        RouterInterface $router,
        $route,
        $host,
        UserServiceInterface $userService)
    {
        $this->contentMailer = $contentMailer;
        $this->router        = $router;
        $this->route         = $route;
        $this->host          = $host;
        $this->userService   = $userService;
    }

    /**
     * @param UserToken   $token
     * @param string      $locale
     * @param string|null $route (optional route to use, instead of default)
     *
     * @return void
     */
    public function sendLoginMail(UserToken $token, $locale, $route = null)
    {
        $userToken = $token->getBearerToken();
        $user      = $token->getUser();

        $link = $this->router->generate(
            Option::fromValue($route)->getOrElse($this->route),
            array('locale' => $locale, 'identifier' => $user->getHandle(), 'token' => $userToken),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $data = array(
            'loginLink' => $link,
            'firstname' => $user->getFirstname(),
            'surname'   => $user->getSurname(),
        );

        $code   = 'login';
        $to     = $user->getEmail();
        $toName = $user->getFirstname() . ' ' . $user->getSurname();

        $this->contentMailer->sendContentTemplateMail($code, $locale, $to, $toName, $data);
    }

    public function onLoginLinkRequested(UserTokenEvent $event)
    {
        $host = $event->getHttpHost();
        if (strpos($host, ':') > 0) {
            list($host,) = explode(':', $host, 2);
        }
        if ($host != $this->host) {
            return;
        }
        $this->sendLoginMail($event->getUserToken(), $event->getLocale(), $event->getRoute());
    }
}
