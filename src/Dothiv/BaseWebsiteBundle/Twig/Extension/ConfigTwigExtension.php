<?php

namespace Dothiv\BaseWebsiteBundle\Twig\Extension;

use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;

class ConfigTwigExtension extends \Twig_Extension
{

    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepo;

    /**
     * @param ConfigRepositoryInterface $configRepo
     */
    public function __construct(ConfigRepositoryInterface $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dothiv_basewebsite_config';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('config', array($this, 'getConfig'))
        );
    }

    public function getConfig($key, $cast = null)
    {
        $value = $this->configRepo->get($key);
        switch ($cast) {
            case 'int':
            case 'integer':
            case 'd':
                return (int)$value->getValue();
            case 'float':
            case 'f':
                return floatval($value->getValue());
            default:
                return $value->getValue();
        }
    }

} 
