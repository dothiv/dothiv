<?php

namespace DotHiv\BusinessBundle\Service;

use DotHiv\BusinessBundle\Entity\Domain;

class DnsConfig implements IDnsConfig {

    protected $mailer;

    public function __construct(\Swift_Mailer $mailer) {
        $this->mailer = $mailer;
        if ($mailer == null)
            throw new \Exception();
    }

    public function forward(Domain $domain) {
        $message = \Swift_Message::newInstance()
            ->setSubject('ok dns, setup ' . $domain->getName())
            ->setFrom('debug-dnsconfigservice@example.com')
            ->setTo('someone@example.com')
            ->setBody('');
        $this->mailer->send($message);
    }

    public function reset(Domain $domain) {
        $message = \Swift_Message::newInstance()
            ->setSubject('ok dns, reset ' . $domain->getName())
            ->setFrom('debug-dnsconfigservice@example.com')
            ->setTo('someone@example.com')
            ->setBody('');
        $this->mailer->send($message);
    }

}
