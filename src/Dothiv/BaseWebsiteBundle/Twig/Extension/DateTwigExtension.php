<?php

namespace Dothiv\BaseWebsiteBundle\Twig\Extension;

class DateTwigExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('date_w3c', array($this, 'dateW3c'))
        );
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function dateW3c($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format(DATE_W3C);
        }
        if (is_int($value) || preg_match('/^[0-9]+$/', $value) !== 0) {
            $value = '@' . $value;
        }
        return $this->dateW3c(new \DateTime($value));
    }

    public function getName()
    {
        return 'dothiv_basewebsite_date';
    }
} 
