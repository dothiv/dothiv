<?php

namespace Dothiv\BaseWebsiteBundle\Service;

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
    public function decimalFormat($value, $locale = null)
    {
        switch ($locale) {
            case 'de':
                return sprintf('%s €', $this->numberFormat->decimalFormat($value, $locale));
            default:
                return sprintf('€%s', $this->numberFormat->decimalFormat($value, $locale));
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
                    return sprintf('%s ct', $this->numberFormat->format($value, $locale));
                }
                return sprintf('%s €', $this->numberFormat->format($value, $locale));
            default:
                if (floatval($value) < 0.01) {
                    return sprintf('€%s¢', $this->numberFormat->format($value, $locale));
                }
                return sprintf('€%s', $this->numberFormat->format($value, $locale));
        }
    }
}
