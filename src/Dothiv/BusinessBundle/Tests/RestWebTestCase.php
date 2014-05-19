<?php

namespace Dothiv\BusinessBundle\Tests;

use Symfony\Component\BrowserKit\Client;
use Dothiv\BusinessBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\PersistentObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base class for tests that use a database and HTTP requests.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
abstract class RestWebTestCase extends WebTestCase {

    /**
     * The HTTP client that is used to make requests.
     * @var Client
     */
    protected static $client;

    /**
     * The HTTP headers used by default.
     * @var array
     */
    private static $default_headers = array(
                                               'CONTENT_TYPE' => 'application/json',
                                               'HTTP_ACCEPT' => 'application/json',
                                           );

    /**
     * Do a JSON-Request, that is, send the $content JSON-encoded to the server with
     * Content-Type- and Accept-Headers set to 'application/json'.
     * 
     * An array of (object, int) with the JSON-response and the status code will be returned.
     * 
     * @param string $method
     * @param string $path
     * @param object|array|string $content The content to be sent. Array and object will be sent JSON-encoded; strings will be sent without modification.
     * 
     * @return array
     */
    protected static function jsonRequest($method, $path, $content = '', $additional_headers = array()) {
        if ($content === null) $content = "";
        if (is_array($content)) $content = (object)$content;

        if (!self::$client) {
            self::$client = static::createClient();
        }

        $crawler = self::$client->request(
                $method,
                $path,
                array(),
                array(),
                array_merge(self::$default_headers, $additional_headers),
                is_object($content) ? json_encode($content) : $content
            );

        $response = json_decode(self::$client->getResponse()->getContent());
        $status = self::$client->getResponse()->getStatusCode();

        return array($response, $status);
    }

}
