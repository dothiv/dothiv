<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function __construct($environment, $debug)
    {
        date_default_timezone_set('Europe/Berlin');
        setlocale(LC_ALL, 'en_US.UTF-8');
        parent::__construct($environment, $debug);
    }

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Dothiv\BusinessBundle\DothivBusinessBundle(),
            new Dothiv\APIBundle\DothivAPIBundle(),
            new Dothiv\CharityWebsiteBundle\DothivCharityWebsiteBundle(),
            new Dothiv\RegistryWebsiteBundle\DothivRegistryWebsiteBundle(),
            new Dothiv\BaseWebsiteBundle\DothivBaseWebsiteBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Dothiv\ContentfulBundle\DothivContentfulBundle(),
            new Dothiv\Bundle\ParsedownBundle\DothivParsedownBundle(),
            new Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle(),
            new Dothiv\AngularJsBundle\DothivAngularJsBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Dothiv\QLPPartnerBundle\DothivQLPPartnerBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Dothiv\PremiumConfiguratorBundle\DothivPremiumConfiguratorBundle(),
            new Dothiv\AfiliasImporterBundle\DothivAfiliasImporterBundle(),
            new Dothiv\AdminBundle\DothivAdminBundle(),
            new Dothiv\PayitforwardBundle\DothivPayitforwardBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
