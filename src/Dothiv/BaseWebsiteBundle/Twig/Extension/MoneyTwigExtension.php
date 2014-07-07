<?php

namespace Dothiv\BaseWebsiteBundle\Twig\Extension;

use Dothiv\BaseWebsiteBundle\Service\MoneyFormatServiceInterface;
use PhpOption\Option;

/**
 * Provides the money and decimalMoney Twig Filters via the {@link MoneyFormatServiceInterface|MoneyFormatService}.
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class MoneyTwigExtension extends \Twig_Extension
{
    /**
     * @var MoneyFormatServiceInterface
     */
    private $moneyFormatService;

    /**
     * @param MoneyFormatServiceInterface $moneyFormatService
     */
    public function __construct(MoneyFormatServiceInterface $moneyFormatService)
    {
        $this->moneyFormatService = $moneyFormatService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('money', array($this, 'money'), array('needs_context' => true)),
            new \Twig_SimpleFilter('moneyDecimal', array($this, 'decimalMoney'), array('needs_context' => true)),
        );
    }

    public function money(array $ctx, $value, $locale = null)
    {
        return $this->moneyFormatService->format($value, Option::fromValue($locale)->getOrElse($ctx['locale']));
    }

    public function decimalMoney(array $ctx, $value, $locale = null)
    {
        return $this->moneyFormatService->decimalFormat($value, Option::fromValue($locale)->getOrElse($ctx['locale']));
    }

    public function getName()
    {
        return 'dothiv_basewebsite_money';
    }
} 
