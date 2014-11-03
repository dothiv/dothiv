<?php

namespace Dothiv\BaseWebsiteBundle\Twig\Extension;

class ShuffleTwigExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('shuffle', array($this, 'shuffle'))
        );
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function shuffle(array $array)
    {
        shuffle($array);
        return $array;
    }

    public function getName()
    {
        return 'dothiv_basewebsite_shuffle';
    }
} 
