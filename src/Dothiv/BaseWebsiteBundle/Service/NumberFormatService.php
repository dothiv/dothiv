<?php

namespace Dothiv\BaseWebsiteBundle\Service;

class NumberFormatService implements NumberFormatServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function decimalFormat($value, $locale = null)
    {
        switch ($locale) {
            case 'de':
                return number_format($value, 0, ',', '.');
            default:
                return number_format($value, 0, '.', ',');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function format($value, $locale = null)
    {
        switch ($locale) {
            case 'de':
                if (floatval($value) < 0.01) {
                    return number_format($value * 100, 1, ',', '.');
                }
                return number_format($value, 2, ',', '.');
            default:
                if (floatval($value) < 0.01) {
                    return number_format($value * 100, 1, '.', ',');
                }
                return number_format($value, 2, '.', ',');
        }
    }
}
