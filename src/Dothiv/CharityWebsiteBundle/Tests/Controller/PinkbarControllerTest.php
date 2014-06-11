<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectsControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function itShouldBeDisabled()
    {
        $client   = static::createClient();
        $this->assertFalse($client->getKernel()->getContainer()->getParameter('dothiv_charity_website.features')['projects']['enabled']);
        $domain = $client->getKernel()->getContainer()->getParameter('charitydomain');
        $client->request('GET', sprintf('http://%s/en/projects', $domain));
        $this->assertEquals(
            404,
            $client->getResponse()->getStatusCode()
        );
    }
} 
