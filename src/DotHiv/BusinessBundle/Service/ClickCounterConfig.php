<?php

namespace DotHiv\BusinessBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

use DotHiv\BusinessBundle\Entity\Banner;
use DotHiv\BusinessBundle\Entity\Domain;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class ClickCounterConfig implements IClickCounterConfig {

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var ObjectManager
     */
    protected $om;

    public function __construct(ContainerInterface $container, EngineInterface $templating, Translator $translator, ObjectManager $om, \Swift_Mailer $mailer) {
        $this->container = $container;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->om = $om;
    }

    public function setup(Domain $domain, Banner $banner) {
        // render config
        $this->translator->setLocale($banner->getLanguage());
        $config = $this->templating->render('DotHivBusinessBundle:Clickcounter:config.txt.twig',
                                            array('banner' => $banner));
        // do clickcounter API request
        list($status, $response) = $this->postConfig($domain->getName(), $config);

        if ($status < 200 || $status >= 300)
            throw new ClickCounterException('setup domain ' . $domain->getName() . ' messed up, server responded status ' . $status);
    }

    public function reset(Domain $domain) {
        // TODO this is dead code, it's never called (there's no GUI for this one)
        // do clickcounter API request
        list($status, $response) = $this->deleteConfig($domain->getName());

        $message = \Swift_Message::newInstance()
            ->setSubject('ok click counter, reset ' . $domain->getName())
            ->setFrom('debug-clickcounterconfigservice@example.com')
            ->setTo('someone@example.com')
            ->setBody('');
        $this->mailer->send($message);

        if ($status < 200 || $status >= 300)
            throw new ClickCounterException('reset of domain ' . $domain->getName() . ' messed up, server responded status ' . $status);
    }

    public function retrieveByDomain(Domain $domain) {
        $this->updateDomain($domain);
        $this->om->flush();
    }

    public function retrieveByDate(\DateTime $notSince) {
        $repo = $this->om->getRepository('DotHivBusinessBundle:Domain');
        $query = $repo->createQueryBuilder('d')
                    ->where('d.lastUpdate <= :notSince')
                    ->orWhere('d.lastUpdate is null')
                    ->setParameter('notSince', $notSince)
                    ->getQuery();
        $domains = $query->getResult();
        $fail = 0;
        foreach($domains as $domain) {
            try {
                $this->updateDomain($domain);
            } catch (ClickCounterException $ex) {
                $fail++;
            }
        }
        $this->om->flush();
        return array(count($domains), $fail);
    }

    /**
     * Read the domain values from the click counter API and
     * update the $domain object.
     *
     * @param Domain $domain
     * @throws ClickCounterException
     */
    private function updateDomain(Domain $domain) {
        // retrieve information
        list($status, $response) = $this->getConfig($domain->getName());

        if ($status < 200 || $status >= 300)
            throw new ClickCounterException('cannot retrieve domain ' . $domain->getName() . ', server responded status ' . $status);

        $information = json_decode($response);

        // save information
        $domain->setClickcount($information->clickcount);
    }

    /**
     * Contact the click counter API to get domain information.
     *
     * This function uses cURL to do a GET request to the /config/{domain name}
     * API endpoint.
     *
     * @param unknown_type $domainname
     */
    private function getConfig($domainname) {
        $ch = curl_init($this->container->getParameter('clickcounter.base_url') . '/config/' . $domainname);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array($status, $response);
    }

    /**
     * Contact the click counter API to set up a new domain configuration.
     *
     * This function uses cURL to do a POST request to the /config/{domain name}
     * API endpoint.
     *
     * @param string $domainname The name of the domain to be POSTed
     * @param string $config The text/plain configuration to POST.
     * @return multitype:mixed array of return status code and response.
     */
    private function postConfig($domainname, $config) {
        $ch = curl_init($this->container->getParameter('clickcounter.base_url') . '/config/' . $domainname);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $config);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array($status, $response);
    }

    /**
     * Contact the click counter API to DELETE a domain configuration.
     *
     * This function uses cURL to do a DELETE request to the /config/{domain name}
     * API endpoint.
     *
     * @param string $domainname The name of the domain to be DELETEed
     */
    private function deleteConfig($domainname) {
        $ch = curl_init($this->container->getParameter('clickcounter.base_url') . '/config/' . $domainname);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); // This is a 'custom' verb? wtf...
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array($status, $response);
    }

    /**
     * Returns the headers used for the HTTP connection to the click counter API.
     * This includes the Authorization header.
     */
    private function getHeaders() {
        $username = $this->container->getParameter('clickcounter.authorization.username');
        $password = $this->container->getParameter('clickcounter.authorization.password');
        return array(
                        'Content-type: text/plain',
                        'Authorization: Basic ' . base64_encode($username . ':' . $password),
                      );
    }

}
