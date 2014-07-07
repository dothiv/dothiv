<?php

namespace Dothiv\BaseWebsiteBundle\Service;

/**
 * {@inheritdoc}
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class MoneyFormatService implements MoneyFormatServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function decimalFormat($value, $locale = null)
    {
        switch ($locale) {
            case 'de':
                return sprintf('%s €', number_format($value, 0, ',', '.'));
            default:
                return sprintf('€%s', number_format($value, 0, '.', ','));
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
                    return sprintf('%s ct', number_format($value * 100, 1, ',', '.'));
                }
                return sprintf('%s €', number_format($value, 2, ',', '.'));
            default:
                if (floatval($value) < 0.01) {
                    return sprintf('€%s¢', number_format($value * 100, 1, '.', ','));
                }
                return sprintf('€%s', number_format($value, 2, '.', ','));
        }
    }
} 
