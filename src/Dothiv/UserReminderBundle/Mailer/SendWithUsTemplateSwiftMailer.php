<?php


namespace Dothiv\UserReminderBundle\Mailer;

use Dothiv\UserReminderBundle\SendWithUs\TemplateRenderer;
use Dothiv\ValueObject\EmailValue;

class SendWithUsTemplateSwiftMailer implements TemplateMailerInterface
{
    /**
     * @param TemplateRenderer $renderer
     * @param \Swift_Mailer    $mailer
     * @param string           $emailFromAddress
     * @param string           $emailFromName
     */
    public function __construct(
        TemplateRenderer $renderer,
        \Swift_Mailer $mailer,
        $emailFromAddress,
        $emailFromName
    )
    {
        $this->mailer           = $mailer;
        $this->renderer         = $renderer;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName    = $emailFromName;
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailValue $recipientAddress, $recipientName, $templateName, $locale, array $data)
    {
        list($templateId, $versionId) = explode('@', $templateName);
        $message = \Swift_Message::newInstance();
        $message
            ->setFrom($this->emailFromAddress, $this->emailFromName)
            ->setTo($recipientAddress->toScalar(), $recipientName);

        $this->renderer->render($message, $data, $templateId, $versionId);

        $this->mailer->send($message);
    }
}
