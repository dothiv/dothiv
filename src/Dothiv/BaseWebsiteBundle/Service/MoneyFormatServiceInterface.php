<?php

namespace Dothiv\BaseWebsiteBundle\Service;

/**
 * Format money according to the locale.
 *
 * Default locale / format is english / en.
 */
interface MoneyFormatServiceInterface
{
    /**
     * Format $value as decimal money according to the $locale.
     *
     * @param float  $value
     * @param string $locale
     * @param string $symbol The currency symbol
     *
     * @return string
     */
    public function decimalFormat($value, $locale = null, $symbol = null);

    /**
     * Format $value as money according to the $locale.
     *
     * @param float  $value
     * @param string $locale
     * @param string $symbol The currency symbol
     *
     * @return string
     */
    public function format($value, $locale = null, $symbol = null);
}
