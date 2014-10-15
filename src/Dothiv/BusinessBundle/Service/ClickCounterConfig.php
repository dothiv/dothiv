<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;

class ClickCounterConfig implements ClickCounterConfigInterface
{

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @var Content
     */
    private $content;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var \Parsedown
     */
    private $parsedown;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(
        $config,
        Content $content
    )
    {
        $this->locales   = $config['locales'];
        $this->baseUrl   = $config['baseurl'];
        $this->secret    = $config['secret'];
        $this->content   = $content;
        $this->parsedown = new \Parsedown();
        $this->parsedown->setBreaksEnabled(false);
        $this->client = new Client();
    }

    public function setup(Banner $banner)
    {
        $domain = $banner->getDomain();
        // render config
        $config = $this->buildBannerConfig($banner);

        // do clickcounter API request
        $this->postConfig($domain->getName(), $config);
    }

    /**
     * {@inheritdoc}
     */
    function get(Domain $domain)
    {
        return $this->getConfig($domain->getName());
    }

    protected function getConfig($domainname)
    {
        $ch = curl_init($this->baseUrl . '/config/' . $domainname);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status < 200 || $status >= 300) {
            $e           = new ClickCounterException(
                sprintf(
                    'Failed to read config for "%s"', $domainname
                )
            );
            $e->response = $response;
            throw $e;
        }
        return json_decode($response);
    }

    /**
     * @param Banner $banner
     *
     * @return array
     */
    protected function buildBannerConfig(Banner $banner)
    {
        $config = array(
            'firstvisit'     => $banner->getPosition(),
            'secondvisit'    => $banner->getPositionAlternative(),
            'default_locale' => $banner->getLanguage(),
            'strings'        => array(),
        );
        if ($banner->getRedirectUrl()) {
            $config['redirect_url'] = $banner->getRedirectUrl();
        }
        foreach ($this->locales as $locale) {
            $config['strings'][$locale] = array(
                'heading'      => $this->getString('heading', $locale),
                'shortheading' => $this->getString('shortheading', $locale),
                'money'        => $this->getString('bar', $locale),
            );
        }
        return $config;
    }

    protected function getString($code, $locale)
    {
        $v = $this->content->buildEntry('String', $code, $locale)->value;
        return strip_tags($this->parsedown->text($v), '<strong><em><a>');
    }

    /**
     * Contact the click counter API to set up a new domain configuration.
     *
     * This function uses cURL to do a POST request to the /config/{domain name}
     * API endpoint.
     *
     * @param string $domainname The name of the domain to be POSTed
     * @param string $config     The text/plain configuration to POST.
     *
     * @return void
     * @throws ClickCounterException
     */
    private function postConfig($domainname, $config)
    {
        $response = $this->client->post($this->baseUrl . '/config/' . $domainname, $this->getHeaders(), json_encode($config))->send();
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new ClickCounterException(
                sprintf(
                    'Failed to write config for "%s"', $domainname
                )
            );
        }
    }

    /**
     * Returns the headers used for the HTTP connection to the click counter API.
     * This includes the Authorization header.
     */
    private function getHeaders()
    {
        $secret = $this->secret;
        return array(
            'Content-type: application/json; charset=utf-8',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode(':' . $secret),
        );
    }

    /**
     * Reads the total click count.
     *
     * @return int
     * @throws ClickCounterException
     */
    function getClickCount()
    {
        $ch = curl_init($this->baseUrl . '/stats/clickcount');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status < 200 || $status >= 300) {
            $e           = new ClickCounterException('Failed to read clickcount');
            $e->response = $response;
            throw $e;
        }
        return (int)$response;
    }

    /**
     * @param ClientInterface $client
     *
     * @return self
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        return $this;
    }
}
