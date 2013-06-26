<?php

namespace DotHiv\BusinessBundle\Tests;

use Symfony\Component\BrowserKit\Client;
use DotHiv\BusinessBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\PersistentObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base class for tests that use a database and HTTP requests.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
abstract class DatabaseWebTestCase extends WebTestCase {

    const USER_EMAIL = 'phpunit@example.com';
    const USER_PASSWORD = 'Br=Pr*p6';
    const USER_NAME = 'phpunit';
    const USER_SURNAME = 'phpunit';

    /**
     * The manager that is used to connect to the database.
     * @var Doctrine\ORM\EntityManager
     */
    protected static $em;

    /**
     * The HTTP client that is used to make requests.
     * @var Client
     */
    protected static $client;

    private static $_application;

    /**
     * Creates a new, empty database. Execution of this method may be expensive.
     */
    protected static function resetDatabase() {
        self::$client = static::createClient();

        self::$_application = new \Symfony\Bundle\FrameworkBundle\Console\Application(static::$kernel);
        self::$_application->setAutoExit(false);
        self::runConsole("doctrine:schema:drop", array("--force" => true));
        self::runConsole("doctrine:schema:create");
        self::runConsole("cache:warmup");

        static::$kernel = static::createKernel();
        static::$kernel->boot();
        self::$em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        self::$client = static::createClient();
    }

    /**
     * Register a new user and log in.
     */
    protected function registerAndLogin() {
        $username = $this->register();
        $this->login();
        return $username;
    }

    /**
     * Register a new user with the details from the class constants.
     * Returns the assigned, random username.
     * 
     * @return string
     */
    protected function register() {
        list($r, $s) = self::jsonRequest('POST', '/api/users', 
                array(
                        'name' => $this::USER_NAME,
                        'surname' => $this::USER_SURNAME,
                        'email' => $this::USER_EMAIL,
                        'plainPassword' => $this::USER_PASSWORD
                        )
                );
        $this->assertEquals(201, $s);
        return $r->username;
    }

    /**
     * Log in with the credentials from the class constants.
     */
    protected function login() {
        list($r, $s) = self::jsonRequest('POST', '/api/login',
                array(
                        'username' => $this::USER_EMAIL,
                        'password' => $this::USER_PASSWORD
                        )
                );
        $this->assertEquals(201, $s);
    }

    /**
     * Log out.
     */
    protected function logout() {
        list($r, $s) = self::jsonRequest('DELETE', '/api/login');

        $this->assertEquals(200, $s);
    }

    private static function runConsole($command, Array $options = array()) {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return self::$_application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    /**
     * Do a JSON-Request, that is, send the $content JSON-encoded to the server with
     * Content-Type- and Accept-Headers set to 'application/json'.
     * 
     * After any request, the EntityManager of this class will be cleared to avoid
     * concurrency between this Manager and the Manager that is used in the actual
     * application.
     * 
     * An array of (object, int) with the JSON-response and the status code will be returned.
     * 
     * @param string $method
     * @param string $path
     * @param object|array|string $content The content to be sent. Array and object will be sent JSON-encoded; strings will be sent without modification.
     * 
     * @return array
     */
    protected static function jsonRequest($method, $path, $content = '') {
        if (is_array($content)) $content = (object)$content;

        $crawler = self::$client->request(
                $method,
                $path,
                array(),
                array(),
                array(
                        'CONTENT_TYPE' => 'application/json',
                        'HTTP_ACCEPT' => 'application/json',
                ),
                is_object($content) ? json_encode($content) : $content
            );

        $response = json_decode(self::$client->getResponse()->getContent());
        $status = self::$client->getResponse()->getStatusCode();

        self::$em->clear();

        return array($response, $status);
    }

}
