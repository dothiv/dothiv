<?php

namespace Dothiv\CharityWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\Businessbundle\Event\UserEvent;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class LoginMailer
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
     * @param User $user
     *
     * @return void
     */
    public function sendLoginMail(User $user)
    {
        $userToken = $user->getBearerToken();

        $link = $this->router->generate(
            'dothiv_charity_account_index',
            array('locale' => 'en', 'handle' => $user->getHandle()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $link .= '#' . $userToken;

        $data = array(
            'loginLink' => $link
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

    public function onLoginLinkRequested(UserEvent $event)
    {
        $this->sendLoginMail($event->getUser());
    }
}
