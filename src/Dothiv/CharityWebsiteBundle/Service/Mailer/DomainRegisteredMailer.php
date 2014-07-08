<?php

namespace Dothiv\CharityWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class DomainRegisteredMailer
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
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @param \Swift_Mailer           $mailer
     * @param RouterInterface         $router
     * @param Content                 $content
     * @param UserRepositoryInterface $userRepo
     * @param string                  $emailFromAddress
     * @param string                  $emailFromName
     */
    public function __construct(
        \Swift_Mailer $mailer,
        RouterInterface $router,
        UserRepositoryInterface $userRepo,
        Content $content,
        $emailFromAddress,
        $emailFromName)
    {
        $this->mailer           = $mailer;
        $this->router           = $router;
        $this->userRepo         = $userRepo;
        $this->content          = $content;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName    = $emailFromName;
    }

    /**
     * @param Domain $domain
     *
     * @return Domain
     */
    public function sendRegisteredDomainMail(Domain $domain)
    {
        $userRepo = $this->userRepo;
        /* @var User $user */
        $user = $userRepo->getUserByEmail($domain->getOwnerEmail())->getOrCall(function () use ($domain, $userRepo) {
            $user = new User();
            $user->setEmail($domain->getOwnerEmail());
            $owner = $domain->getOwnerName();
            if ($pos = strrpos($owner, ' ')) {
                $user->setSurname(trim(substr($owner, 0, $pos)));
                $user->setName(trim(substr($owner, $pos)));
            } else {
                $user->setName($owner);
            }
            $userRepo->persist($user)->flush();
            return $user;
        });

        $userToken = $user->getBearerToken();
        $userRepo->persist($user)->flush();

        $link = $this->router->generate(
            'dothiv_charity_account',
            array('locale' => 'en', 'handle' => $user->getHandle()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $link .= '#' . $userToken;

        $data = array(
            'domainName' => $domain->getName(),
            'ownerName'  => $domain->getOwnerName(),
            'ownerEmail' => $domain->getOwnerEmail(),
            'loginLink'  => $link,
            'claimToken' => $domain->getToken(),
        );

        $template = $this->content->buildEntry('eMail', 'domain.registered', 'en');

        $twig    = new \Twig_Environment(new \Twig_Loader_String());
        $subject = $twig->render($template->subject, $data);
        $text    = $twig->render($template->text, $data);

        // send email
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($subject)
            ->setFrom($this->emailFromAddress, $this->emailFromName)
            ->setTo($domain->getOwnerEmail(), $domain->getOwnerName())
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

        return $domain;
    }

    public function onDomainRegistered(DomainEvent $event)
    {
        $this->sendRegisteredDomainMail($event->getDomain());
    }
}
