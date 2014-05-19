<?php

/**
 * Controller for the index page.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 * @copyright 2014 TLD dotHIV Registry GmbH | http://dothiv-registry.net/
 */

namespace Dothiv\BaseWebsiteBundle\Translation;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class JsonLoader implements LoaderInterface
{
    public function load($resource, $locale, $domain = 'messages')
    {
        $strings = json_decode(file_get_contents($resource));

        $catalogue = new MessageCatalogue($locale);
        $this->registerStrings($strings, $catalogue, $domain);
        return $catalogue;
    }

    protected function registerStrings(\stdClass $strings, MessageCatalogue $catalogue, $domain, $parent = null)
    {
        if ($parent != null) {
            $parent .= '.';
        }
        foreach ($strings as $k => $v) {
            if (is_object($v)) {
                $this->registerStrings($v, $catalogue, $domain, $parent . $k);
            } else {
                $catalogue->set(strtolower($parent . $k), $v, $domain);
            }
        }
    }
}
