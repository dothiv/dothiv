<?php


namespace Dothiv\UserReminderBundle\Message;

interface UserReminderMessageInterface
{
    /**
     * Sends the message.
     *
     * @param \Swift_Mailer $mailer
     */
    function send(\Swift_Mailer $mailer);
}
