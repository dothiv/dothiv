<?php

namespace Dothiv\CharityWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\Businessbundle\Event\UserEvent;
use Dothiv\BusinessBundle\Event\UserTokenEvent;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class LoginLinkMailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var string
     */
    private $emailFromAddress;

    /**
     * @var string
     */
    private $emailFromName;

    /**
     * @var Content
     */
    private $content;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @param \Swift_Mailer        $mailer
     * @param RouterInterface      $router
     * @param Content              $content
     * @param UserServiceInterface $userService
     * @param string               $emailFromAddress
     * @param string               $emailFromName
     */
    public function __construct(
        \Swift_Mailer $mailer,
        RouterInterface $router,
        UserServiceInterface $userService,
        Content $content,
        $emailFromAddress,
        $emailFromName)
    {
        $this->mailer           = $mailer;
        $this->router           = $router;
        $this->userService      = $userService;
        $this->content          = $content;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName    = $emailFromName;
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
            'dothiv_charity_account_index',
            array('locale' => 'en'),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $link .= sprintf('#!/auth/%s/%s', $user->getHandle(), $userToken);

        $data = array(
            'loginLink' => $link,
            'surname'   => $user->getSurname(),
            'name'      => $user->getName(),
        );

        $template = $this->content->buildEntry('eMail', 'login', 'en');

        $twig    = new \Twig_Environment(new \Twig_Loader_String());
        $subject = $twig->render($template->subject, $data);
        $text    = $twig->render($template->text, $data);

        // send email
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($subject)
            ->setFrom($this->emailFromAddress, $this->emailFromName)
            ->setTo($user->getEmail(), $user->getSurname() . ' ' . $user->getName())
            ->setBody($text);

        // Add HTML part.
        if (property_exists($template, 'html')) {
            $html = '';
            if (property_exists($template, 'htmlHead')) {
                $html .= $template->htmlHead;
            }
            $parsedown = new \Parsedown();
            $html .= $twig->render($parsedown->text($template->html), $data);
            if (property_exists($template, 'htmlFoot')) {
                $html .= $template->htmlFoot;
            }
            $message->addPart($html, 'text/html');
        }

        $this->mailer->send($message);
    }

    public function onLoginLinkRequested(UserTokenEvent $event)
    {
        $this->sendLoginMail($event->getUserToken());
    }
}
