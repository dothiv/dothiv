<?php

namespace Dothiv\RegistryWebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function preregisterAction(Request $request)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('New domain pre-registration')
            ->setFrom('m@dothiv.org')
            ->setTo('m@dothiv.org')
            ->setBody(print_r(json_decode($request->getContent()), true));
        $this->mailer->send($message);
        $response = new Response();
        return $response;
    }
}
