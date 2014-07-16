<?php

namespace Dothiv\BaseWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\Content;

class ContentMailer implements ContentMailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

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
     * @param \Swift_Mailer $mailer
     * @param Content       $content
     * @param string        $emailFromAddress
     * @param string        $emailFromName
     */
    public function __construct(
        \Swift_Mailer $mailer,
        Content $content,
        $emailFromAddress,
        $emailFromName)
    {
        $this->mailer           = $mailer;
        $this->content          = $content;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName    = $emailFromName;
    }

    /**
     * @param string $code
     * @param string $locale
     * @param string $to
     * @param string $toName
     * @param array  $data
     */
    public function sendContentTemplateMail($code, $locale, $to, $toName, $data)
    {
        $template = $this->content->buildEntry('eMail', $code, $locale);
        $twig    = new \Twig_Environment(new \Twig_Loader_String());
        $subject = $twig->render($template->subject, $data);
        $text    = $twig->render($template->text, $data);

        // send email
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($subject)
            ->setFrom($this->emailFromAddress, $this->emailFromName)
            ->setTo($to, $toName)
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
} 
