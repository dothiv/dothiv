<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class Registration implements IRegistration {

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container, ObjectManager $om, \Swift_Mailer $mailer, EngineInterface $templating) {
        $this->om = $om;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->container = $container;
    }

    public function registered($name, $email) {
        // check if domain is already known
        if ($this->om->getRepository('DothivBusinessBundle:Domain')->findBy(array('name' => $name)))
            throw new RegistrationException('Tried to register already registered domain ' . $name . ' for ' . $email . '.');

        // create token
        $token = $this->generateToken();

        // create domain object
        $domain = new Domain();
        $domain->setName($name);
        $domain->setEmailAddressFromRegistrar($email);
        $domain->setClaimingToken($token);

        // save domain
        $this->om->persist($domain);
        $this->om->flush();

        // send email
        $message = \Swift_Message::newInstance()
            ->setSubject($this->templating->render('DothivBusinessBundle:Emails:DomainMailSubject.txt.twig', array('domain' => $domain)))
            ->setFrom($this->container->getParameter('domain_email_sender_address'))
            ->setTo($domain->getEmailAddressFromRegistrar())
            ->setBody($this->templating->render('DothivBusinessBundle:Emails:DomainMailBody.txt.twig', array('domain' => $domain)));
        $this->mailer->send($message);

        return $domain;
    }

    public function deleted($name) {
        $domains = $this->om->getRepository('DothivBusinessBundle:Domain')->findBy(array('name' => $name));

        if (count($domains) == 0) 
           throw new RegistrationException('Tried to delete not-registered domain ' . $name . '.');

        $this->om->remove($domains[0]);
        $this->om->flush();
    }

    public function transferred($name, $email) {
        $this->deleted($name);
        return $this->registered($name, $email);
    }

    private function generateToken() {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

}
