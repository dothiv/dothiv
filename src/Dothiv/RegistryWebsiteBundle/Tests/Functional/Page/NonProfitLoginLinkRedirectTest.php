<?php

namespace Dothiv\RegistryWebsiteBundle\Tests\Functional\Page;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NonProfitLoginLinkRedirectTest extends WebTestCase
{
    /**
     * @test
     * @group        RegistryWebsiteBundle
     * @group        Controller
     * @group        Integration
     */
    public function itShouldRedirectToAppAuth()
    {
        $client = static::createClient();
        $client->request('GET', 'https://tld.hiv.dev/en/register-non-profit-form/auth/userhandle/logintoken');
        $response = $client->getResponse();
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('https://tld.hiv.dev/en/register-non-profit-form#!/auth/userhandle/logintoken', $response->headers->get('Location'));
    }
} 
