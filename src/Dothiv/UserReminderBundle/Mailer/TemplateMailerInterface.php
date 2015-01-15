<?php

namespace Dothiv\UserReminderBundle\Mailer;

use Dothiv\ValueObject\EmailValue;

interface TemplateMailerInterface
{
    /**
     * @param EmailValue $recipientAddress
     * @param string     $recipientName
     * @param string     $templateName
     * @param string     $locale
     * @param array      $data
     *
     * @return mixed
     */
    function send(EmailValue $recipientAddress, $recipientName, $templateName, $locale, array $data);
}
