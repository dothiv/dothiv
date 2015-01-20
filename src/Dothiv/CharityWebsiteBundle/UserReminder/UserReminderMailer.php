<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder;

use Dothiv\CharityWebsiteBundle\SendWithUs\TemplateRenderer;
use Dothiv\ValueObject\EmailValue;

class UserReminderMailer
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TemplateRenderer
     */
    private $renderer;

    /**
     * @var EmailValue
     */
    private $emailFromAddress;

    /**
     * @var string
     */
    private $emailFromName;

    /**
     * @param \Swift_Mailer    $mailer
     * @param TemplateRenderer $renderer
     * @param string           $emailFromAddress
     * @param string           $emailFromName
     */
    public function __construct(
        \Swift_Mailer $mailer,
        TemplateRenderer $renderer,
        $emailFromAddress,
        $emailFromName
    )
    {
        $this->mailer           = $mailer;
        $this->renderer         = $renderer;
        $this->emailFromAddress = new EmailValue($emailFromAddress);
        $this->emailFromName    = $emailFromName;
    }

    /**
     * @param array      $data
     * @param EmailValue $to
     * @param string     $recipientName
     * @param string     $templateId
     * @param string     $versionId
     */
    public function send(array $data, EmailValue $to, $recipientName, $templateId, $versionId)
    {
        $message = \Swift_Message::newInstance();
        $message
            ->setFrom($this->emailFromAddress->toScalar(), $this->emailFromName)
            ->setTo($to->toScalar(), $recipientName);

        $this->renderer->render($message, $data, $templateId, $versionId);

        $this->mailer->send($message);
    }
}
