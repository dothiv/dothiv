<?php


namespace Dothiv\BusinessBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Service\WhoisReportParser;

class WhoisReportParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group WHOIS
     * @group Service
     * @group BusinessBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Service\WhoisReportParser', $this->createTestObject());
    }

    /**
     * @test
     * @group   WHOIS
     * @group   Service
     * @group   BusinessBundle
     * @depends itShouldBeInstantiable
     */
    public function itShouldParseAWhoisReport()
    {
        $report = $this->createTestObject()->parse(file_get_contents(__DIR__ . '/data/tld.hiv.whois'));
        $this->assertEquals('TLD.HIV', $report->get('Domain Name'));
        $this->assertEquals('TLD dotHIV Registry GmbH', $report->get('Registrant Name'));
        $this->assertEquals('domains@tld.hiv', $report->get('Registrant Email'));
        $nameservers = new ArrayCollection(array(
            'NS-CLOUD-E1.GOOGLEDOMAINS.COM',
            'NS-CLOUD-E2.GOOGLEDOMAINS.COM',
            'NS-CLOUD-E3.GOOGLEDOMAINS.COM',
            'NS-CLOUD-E4.GOOGLEDOMAINS.COM'
        ));
        $this->assertEquals($nameservers, $report->get('Name Server'));
    }

    /**
     * @return WhoisReportParser
     */
    protected function createTestObject()
    {
        return new WhoisReportParser();
    }
}
