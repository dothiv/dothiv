<?php

namespace Dothiv\CharityWebsiteBundle\Twig\Extension;

class FeaturesTwigExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $features;

    /**
     * @param array $features associative array of features array('featurename' => bool)
     */
    public function __construct(array $features)
    {
        $this->features = $features;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        $features = array();
        foreach ($this->features as $name => $config) {
            $features[$name] = $config['enabled'];
        }
        return array('features' => $features);
    }

    public function getName()
    {
        return 'dothiv_charitywebsite_features';
    }
} 
