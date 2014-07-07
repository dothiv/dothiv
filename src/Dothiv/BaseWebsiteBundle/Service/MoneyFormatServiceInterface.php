<?php

namespace Dothiv\BaseWebsiteBundle\Service;

/**
 * Format money according to the locale.
 *
 * Default locale / format is english / en.
 *
 * NOTE: All money values are in €!
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
interface MoneyFormatServiceInterface
{
    /**
     * Format $value as decimal money according to the $locale.
     *
     * @param $value
     * @param $locale
     *
     * @return string
     */
    public function decimalFormat($value, $locale = null);

    /**
     * Format $value as money according to the $locale.
     *
     * @param $value
     * @param $locale
     *
     * @return string
     */
    public function format($value, $locale = null);
} 
