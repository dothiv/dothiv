<?php


namespace Dothiv\ShopBundle\Service;

use Dothiv\ValueObject\IdentValue;

/**
 * Convert firstnames to correct genitiv form according to the locale
 *
 * See https://trello.com/c/xraEgfs9/238-names-ending-on-s-look-weird-on-4life-landingpage
 */
class GenitivfyService implements GenitivfyServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function genitivfy($name, IdentValue $locale)
    {
        if ($locale->equals(new IdentValue('de'))) {
            $name = $name . "s";
            $name = preg_replace("/([ßzxs])s$/", "$1'", $name);
        }

        if ($locale->equals(new IdentValue('en'))) {
            $name = $name . "'s";
            $name = preg_replace("/s's$/", "s'", $name);
        }
        return $name;
    }
}
