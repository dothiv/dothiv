<?php

namespace Dothiv\APIBundle\Tests\EndToEnd;

use Dothiv\BusinessBundle\Tests\DatabaseRestWebTestCase;

/**
 * Tests the process of registering and claiming a domain.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class DomainRegistrationAndClaimTestTest extends DatabaseRestWebTestCase {

    protected static $username = "";

    public static function setUpBeforeClass() {
        self::resetDatabase();
    }

    public function testSuccessful() {
        // Post domain
        list($r, $s) = self::jsonRequest('POST', '/api/domains',
                array(
                        'name' => 'test.hiv',
                        'emailAddressFromRegistrar' => 'test@test.hiv'
                        )
                );

        $this->assertEquals(201, $s);
        $this->assertEquals("test.hiv", $r->name);
        $this->assertEquals(64, strlen($r->claimingToken));

        $domains = self::$em->getRepository('DothivBusinessBundle:Domain')->findBy(array('claimingToken' => $r->claimingToken));
        $this->assertCount(1, $domains);
        $domain = $domains[0]; /** @var $domain Dothiv\APIBundle\Entity\Domain */
        $this->assertEquals("test.hiv", $domain->getName());
        $this->assertEquals($r->claimingToken, $domain->getClaimingToken());

        // TODO check sent mail, get token from there
        $token = $r->claimingToken;

        // Claim domain
        self::$username = $username = $this->register();
        $this->login();
        list($r, $s) = self::jsonRequest('POST', '/api/domains/claims',
                array(
                        'username' => $username,
                        'claimingToken' => $r->claimingToken
                        )
                );

        $this->assertEquals(201, $s);
        $this->assertEquals("test.hiv", $r->domain);
        $this->assertEquals($username, $r->username);
        $this->assertNotEmpty($r->claimingToken);

        $domains = self::$em->getRepository('DothivBusinessBundle:Domain')->findBy(array('name' => $r->domain));
        $this->assertCount(1, $domains);
        $domain = $domains[0]; /** @var $domain Dothiv\APIBundle\Entity\Domain */
        $this->assertEquals("test.hiv", $domain->getName());
        $this->assertEquals('test@test.hiv', $domain->getEmailAddressFromRegistrar());
        $this->assertNull($domain->getClaimingToken());
        $this->assertNotNull($domain->getOwner());
        $this->assertEquals($username, $domain->getOwner()->getUsername());
    }

    public function testNonExistentUsername() {
        list($r, $s) = self::jsonRequest('POST', '/api/domains/claims',
                array(
                        'username' => 'no such username',
                        'claimingToken' => 'no such token'
                )
        );

        $this->assertEquals(403, $s);
    }

    public function testNonExistentToken() {
        list($r, $s) = self::jsonRequest('POST', '/api/domains/claims',
                array(
                        'username' => self::$username,
                        'claimingToken' => 'no such token'
                )
        );

        $this->assertEquals(400, $s);
    }

    public function testValidClaimButNotLoggedIn() {
        $this->logout();

        // Post domain
        list($r, $s) = self::jsonRequest('POST', '/api/domains',
                array(
                        'name' => 'test2.hiv',
                        'emailAddressFromRegistrar' => 'test2@test.hiv'
                )
        );

        $token = $r->claimingToken;
        $this->assertEquals(201, $s);
        $this->assertNotEmpty($token);

        // Claim domain
        list($r, $s) = self::jsonRequest('POST', '/api/domains/claims',
                array(
                        'username' => self::$username,
                        'claimingToken' => $token
                )
        );

        $this->assertEquals(401, $s);

        $this->login();

        // Claim domain
        list($r, $s) = self::jsonRequest('POST', '/api/domains/claims',
                array(
                        'username' => self::$username,
                        'claimingToken' => $token
                )
        );

        $this->assertEquals(201, $s);
    }
}
