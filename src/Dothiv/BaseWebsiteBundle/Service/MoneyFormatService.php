<?php

namespace Dothiv\BaseWebsiteBundle\Service;

use PhpOption\Option;

class MoneyFormatService implements MoneyFormatServiceInterface
{

    /**
     * @var NumberFormatServiceInterface
     */
    private $numberFormat;

    /**
     * @param NumberFormatServiceInterface $numberFormatService
     */
    public function __construct(NumberFormatServiceInterface $numberFormatService)
    {
        $this->numberFormat = $numberFormatService;
    }

    /**
     * {@inheritdoc}
     */
    public function decimalFormat($value, $locale = null, $symbol = null)
    {
        $symbol = Option::fromValue($symbol)->getOrElse('€');
        switch ($locale) {
            case 'de':
                return sprintf('%s %s', $this->numberFormat->decimalFormat($value, $locale), $symbol);
            default:
                return sprintf('%s%s', $symbol, $this->numberFormat->decimalFormat($value, $locale));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function format($value, $locale = null, $symbol = null)
    {
        $symbol = Option::fromValue($symbol)->getOrElse('€');
        switch ($locale) {
            case 'de':
                if (floatval($value) < 0.01) {
                    return sprintf('%s ct', $this->numberFormat->format($value, $locale));
                } elseif (floatval($value) < 1.0) {
                    return sprintf('%d ct', $this->numberFormat->decimalFormat($value * 100, $locale));
                }
                return sprintf('%s %s', $this->numberFormat->format($value, $locale), $symbol);
            default:
                if (floatval($value) < 0.01) {
                    return sprintf('%s%s¢', $symbol, $this->numberFormat->format($value, $locale));
                } elseif (floatval($value) < 1.0) {
                    return sprintf('%s%d¢', $symbol, $this->numberFormat->decimalFormat($value * 100, $locale));
                }
                return sprintf('%s%s', $symbol, $this->numberFormat->format($value, $locale));
        }
    }
}
