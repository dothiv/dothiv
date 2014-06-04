<?php

namespace Dothiv\BaseWebsiteBundle\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Sanpi\Behatch\Context\BehatchContext;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext extends BehatContext
    implements KernelAwareInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    private $parameters;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('behatch', new BehatchContext($parameters));
        $this->useContext('mink', new MinkContext($parameters));
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
}
