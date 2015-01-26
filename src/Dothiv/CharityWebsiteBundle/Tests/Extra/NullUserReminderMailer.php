<?php


namespace Dothiv\CharityWebsiteBundle\Tests\Extra;

use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\ValueObject\EmailValue;

class NullUserReminderMailer extends UserReminderMailer
{
    /**
     * {@inheritdoc}
     */
    public function send(array $data, EmailValue $to, $recipientName, $templateId, array $attachments = null, $locale = 'en')
    {
        // pass
    }
}
