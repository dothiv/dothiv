<?php

namespace DotHiv\APIBundle\Tests\EndToEnd;

use DotHiv\BusinessBundle\Tests\RestWebTestCase;

/**
 * Tests the process of registering and claiming a domain.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class LocaleControllerTest extends RestWebTestCase {

    public function xtestDefaultLocale() {
        list($r, $s) = self::jsonRequest('GET', '/api/locale',
                null,
                array(
                        'HTTP_ACCEPT_LANGUAGE' => ''
                )
        );

        $this->assertEquals(200, $s);
        $this->assertEquals('en', $r->locale);
    }

    public function xtestUseAcceptHeader() {
        list($r, $s) = self::jsonRequest('GET', '/api/locale',
                null,
                array(
                        'HTTP_ACCEPT_LANGUAGE' => 'de_DE'
                )
        );

        $this->assertEquals(200, $s);
        $this->assertEquals('de_DE', $r->locale);

        list($r, $s) = self::jsonRequest('GET', '/api/locale',
                null,
                array(
                        'HTTP_ACCEPT_LANGUAGE' => 'en_US'
                )
        );

        $this->assertEquals(200, $s);
        $this->assertEquals('en_US', $r->locale);
    }

    public function testSetLocaleManually() {
        list($r, $s) = self::jsonRequest('PUT', '/api/locale',
                array(
                        'locale' => 'en_US'
                )
        );

        $this->assertEquals(204, $s);
        $this->assertEquals(null, $r);

        list($r, $s) = self::jsonRequest('GET', '/api/locale',
                null,
                array(
                        'HTTP_ACCEPT_LANGUAGE' => 'de_DE'
                )
        );

        $this->assertEquals(200, $s);
        $this->assertEquals('en_US', $r->locale);
    }

    public function testUnsetLocale() {
        list($r, $s) = self::jsonRequest('PUT', '/api/locale',
                array(
                        'locale' => ''
                )
        );

        $this->assertEquals(204, $s);
        $this->assertEquals(null, $r);

        list($r, $s) = self::jsonRequest('GET', '/api/locale',
                null,
                array(
                        'HTTP_ACCEPT_LANGUAGE' => 'fr_FR'
                )
        );

        $this->assertEquals(200, $s);
        $this->assertEquals('fr_FR', $r->locale);
    }

}
