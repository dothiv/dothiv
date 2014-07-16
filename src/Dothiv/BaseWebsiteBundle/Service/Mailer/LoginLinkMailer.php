<?php

namespace Dothiv\BaseWebsiteBundle\Service\Mailer;

use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Event\UserTokenEvent;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
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
     * @param UserToken $token
     *
     * @return void
     */
    public function sendLoginMail(UserToken $token)
    {
        $userToken = $token->getBearerToken();
        $user      = $token->getUser();

        $link = $this->router->generate(
            $this->route,
            array('locale' => 'en'),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $link .= sprintf('#!/auth/%s/%s', $user->getHandle(), $userToken);

        $data = array(
            'loginLink' => $link,
            'firstname' => $user->getFirstname(),
            'surname'   => $user->getSurname(),
        );

        $code   = 'login';
        $locale = 'en';
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
        $this->sendLoginMail($event->getUserToken());
    }
}
