<?php

namespace DotHiv\BusinessBundle\Service;

use DotHiv\BusinessBundle\Entity\Banner;
use DotHiv\BusinessBundle\Entity\Domain;

class ClickCounterConfig implements IClickCounterConfig {

    protected $mailer;

    public function __construct(\Swift_Mailer $mailer) {
        $this->mailer = $mailer;
        if ($mailer == null)
            throw new \Exception();
    }

    public function setup(Domain $domain, Banner $banner) {
        $message = \Swift_Message::newInstance()
            ->setSubject('ok click counter, setup ' . $domain->getName())
            ->setFrom('debug-clickcounterconfigservice@example.com')
            ->setTo('someone@example.com')
            ->setBody('');
        $this->mailer->send($message);
    }

    public function reset(Domain $domain) {
        $message = \Swift_Message::newInstance()
            ->setSubject('ok click counter, reset ' . $domain->getName())
            ->setFrom('debug-clickcounterconfigservice@example.com')
            ->setTo('someone@example.com')
            ->setBody('');
        $this->mailer->send($message);
    }

}
