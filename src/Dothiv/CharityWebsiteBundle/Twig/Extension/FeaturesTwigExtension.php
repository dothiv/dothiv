<?php

namespace Dothiv\CharityWebsiteBundle\Twig\Extension;

class FeaturesTwigExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $features;

    /**
     * @var array
     */
    private $bundles;

    /**
     * @param array $features associative array of features array('featurename' => bool)
     * @param array $bundles  list of available bundles
     */
    public function __construct(array $features, array $bundles)
    {
        $this->features = $features;
        $this->bundles  = $bundles;
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
        return array('features' => $features, 'bundles' => $this->bundles);
    }

    public function getName()
    {
        return 'dothiv_charitywebsite_features';
    }
} 
