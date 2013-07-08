<?php

namespace DotHiv\BusinessBundle\Tests;

use Symfony\Component\BrowserKit\Client;
use DotHiv\BusinessBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\PersistentObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Extend the RestWebTestCase class by adding a fresh and
 * temporary database.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
abstract class DatabaseRestWebTestCase extends RestWebTestCase {

    const USER_EMAIL = 'phpunit@example.com';
    const USER_PASSWORD = 'Br=Pr*p6';
    const USER_NAME = 'phpunit';
    const USER_SURNAME = 'phpunit';

    /**
     * The manager that is used to connect to the database.
     * @var Doctrine\ORM\EntityManager
     */
    protected static $em;

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

    protected static function jsonRequest($method, $path, $content = '', $additional_headers = array()) {
        $ret = parent::jsonRequest($method, $path, $content, $additional_headers);
        self::$em->clear();
        return $ret;
    }

}
