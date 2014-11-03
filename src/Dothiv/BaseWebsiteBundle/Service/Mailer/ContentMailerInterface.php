<?php

namespace Dothiv\BaseWebsiteBundle\Service\Mailer;

interface ContentMailerInterface
{
    /**
     * Send a template mail.
     *
     * @param string $code
     * @param string $locale
     * @param string $to
     * @param string $toName
     * @param array  $data
     */
    public function sendContentTemplateMail($code, $locale, $to, $toName, array $data);
}
