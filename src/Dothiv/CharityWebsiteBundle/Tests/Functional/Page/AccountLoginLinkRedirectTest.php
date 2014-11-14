<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Functional\Page;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountLoginLinkRedirectTest extends WebTestCase
{
    /**
     * @test
     * @group        CharityWebsiteBundle
     * @group        Controller
     * @group        Integration
     * @dataProvider localeProvider
     */
    public function itShouldRedirectToAppAuth($locale)
    {
        $client = static::createClient();
        $client->request('GET', 'https://click4life.hiv.dev/' . $locale . '/account/auth/userhandle/logintoken');
        $response = $client->getResponse();
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('https://click4life.hiv.dev/' . $locale . '/account#!/auth/userhandle/logintoken', $response->headers->get('Location'));
    }

    public function localeProvider()
    {
        return array(
            array('en'),
            array('de')
        );
    }
} 
