<?php

namespace Dothiv\APIBundle\Tests\EndToEnd;

use Dothiv\BusinessBundle\Tests\DatabaseRestWebTestCase;

/**
 * Tests the registration and login process.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class UserRegistrationAndLoginTest extends DatabaseRestWebTestCase {

    protected static $username = '';

    public static function setUpBeforeClass() {
        self::resetDatabase();
    }

    public function testPostRegistration() {
        $this->markTestIncomplete('Refactoring pending.');
        list($r, $s) = self::jsonRequest('POST', '/api/users', 
                array(
                        'name' => $this::USER_NAME,
                        'surname' => $this::USER_SURNAME,
                        'email' => $this::USER_EMAIL,
                        'plainPassword' => $this::USER_PASSWORD
                        )
                );

        $this->assertEquals(201, $s);
        $this->assertEquals($this::USER_NAME, $r->name);
        $this->assertEquals($this::USER_SURNAME, $r->surname);
        $this->assertEquals($this::USER_EMAIL, $r->email);
        $this->assertEquals(12, strlen($r->username));

        $users = self::$em->getRepository('DothivBusinessBundle:User')->findBy(array('username' => $r->username));
        $this->assertCount(1, $users);
        $user = $users[0];
        $this->assertEquals($this::USER_EMAIL, $user->getEmail());

        self::$username = $r->username;
    }

    /**
     * @depends testPostRegistration
     */
    public function testDeleteLoginWhenNotLoggedIn() {
        list($r, $s) = self::jsonRequest('DELETE', '/api/login');

        $this->assertEquals(200, $s);
        $this->assertEquals(null, $r);
    }

    /**
     * @depends testPostRegistration
     */
    public function testPostLoginUnsuccessful() {
        list($r, $s) = self::jsonRequest('POST', '/api/login',
                array(
                        'username' => 'no such user',
                        'password' => 'no such password'
                )
        );

        $this->assertEquals(400, $s);
        $this->assertEquals(null, $r);
    }

    /**
     * @depends testPostRegistration
     */
    public function testPostLoginSuccessful() {
        list($r, $s) = self::jsonRequest('POST', '/api/login',
                array(
                        'username' => $this::USER_EMAIL,
                        'password' => $this::USER_PASSWORD
                        )
                );

        $this->assertEquals(201, $s);
        $this->assertEquals($this::USER_EMAIL, $r->email);
        $this->assertEquals($this::USER_NAME, $r->name);
        $this->assertEquals($this::USER_SURNAME, $r->surname);
    }

    /**
     * @depends testPostRegistration
     */
    public function testGetLogin() {
        list($r, $s) = self::jsonRequest('GET', '/api/login');

        $this->assertEquals(200, $s);
        $this->assertEquals($this::USER_EMAIL, $r->email);
        $this->assertEquals($this::USER_NAME, $r->name);
        $this->assertEquals($this::USER_SURNAME, $r->surname);
    }

    /**
     * @depends testPostRegistration
     */
    public function testSecureResourceWhenLoggedIn() {
        list($r, $s) = self::jsonRequest('GET', '/api/users/' . self::$username);

        $this->assertEquals(200, $s);
    }

    /**
     * @depends testPostRegistration
     */
    public function testDeleteLoginWhenLoggedIn() {
        list($r, $s) = self::jsonRequest('DELETE', '/api/login');

        $this->assertEquals(200, $s);
        $this->assertEquals(null, $r);
    }

    /**
     * @depends testPostRegistration
     */
    public function testSecureResourceWhenNotLoggedIn() {
        list($r, $s) = self::jsonRequest('GET', '/api/users/' . self::$username);

        $this->assertEquals(401, $s);
        $this->assertEquals(null, $r);
    }

}
