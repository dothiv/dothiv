<?php

namespace Dothiv\CharityWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Event\DomainEvent;
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
     * @param \Swift_Mailer   $mailer
     * @param RouterInterface $router
     * @param Content         $content
     * @param string          $emailFromAddress
     * @param string          $emailFromName
     */
    public function __construct(\Swift_Mailer $mailer, RouterInterface $router, Content $content, $emailFromAddress, $emailFromName)
    {
        $this->mailer           = $mailer;
        $this->router           = $router;
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
        $data = array(
            'domainName' => $domain->getName(),
            'ownerName'  => $domain->getOwnerName(),
            'ownerEmail' => $domain->getOwnerEmail(),
            'claimLink'  => $this->router->generate(
                    'dothiv_charity_domain_claim',
                    array('locale' => 'en', 'token' => $domain->getToken()),
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
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
