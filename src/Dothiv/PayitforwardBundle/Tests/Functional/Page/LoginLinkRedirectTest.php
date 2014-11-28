<?php

namespace Dothiv\PayitforwardBundle\Tests\Functional\Page;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginLinkRedirectTest extends WebTestCase
{
    /**
     * @test
     * @group        PayitforwardBundle
     * @group        Controller
     * @group        Integration
     * @dataProvider localeProvider
     */
    public function itShouldRedirectToAppAuth($locale)
    {
        $client = static::createClient();
        $client->request('GET', 'https://tld.hiv.dev/' . $locale . '/payitforward/checkout/auth/userhandle/logintoken');
        $response = $client->getResponse();
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('https://tld.hiv.dev/' . $locale . '/payitforward/checkout#!/auth/userhandle/logintoken', $response->headers->get('Location'));
    }

    public function localeProvider()
    {
        return array(
            array('en'),
            array('de')
        );
    }
}
