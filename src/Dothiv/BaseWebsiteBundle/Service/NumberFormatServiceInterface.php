<?php

namespace Dothiv\BaseWebsiteBundle\Service;

/**
 * Format a number according to the locale.
 *
 * Default locale / format is english / en.
 */
interface NumberFormatServiceInterface
{
    /**
     * Format $value as decimal according to the $locale.
     *
     * @param $value
     * @param $locale
     *
     * @return string
     */
    public function decimalFormat($value, $locale = null);

    /**
     * Format $value as floating point number according to the $locale.
     *
     * @param $value
     * @param $locale
     *
     * @return string
     */
    public function format($value, $locale = null);
}
