<?php

namespace DotHiv\BusinessBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use DotHiv\BusinessBundle\Entity\Domain;

class DnsConfig implements IDnsConfig {

    protected $mailer;
    protected $container;

    public function __construct(ContainerInterface $container, \Swift_Mailer $mailer) {
        $this->container = $container;
        $this->mailer = $mailer;
    }

    public function forward(Domain $domain) {
//         $client = new \SoapClient('http://api-ote.domaindiscount24.com:4424/?wsdl');
        $return = '';
//         $return = $client->updateDomain(
//                     new \SoapParam(array('reseller' => $this->container->getParameter('dns.reseller')))
// //                     new \SoapParam($this->container->getParameter('dns.password'), 'password'),
// //                     new \SoapParam($this->container->getParameter('dns.cid'), 'cid'),
// //                     new \SoapParam($domain->getName(), 'domain'),
// //                     new \SoapParam($this->container->getParameter('dns.nameserver'), 'nameserver')
//                 );

        $message = \Swift_Message::newInstance()
            ->setSubject('ok dns, setup ' . $domain->getName())
            ->setFrom('debug-dnsconfigservice@example.com')
            ->setTo('someone@example.com')
            ->setBody(print_r($return, true));
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
