<?php

namespace Dothiv\PayitforwardBundle\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\CommonContexts\DoctrineFixturesContext;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Tools\SchemaTool;
use Sanpi\Behatch\Context\BehatchContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext extends \Dothiv\APIBundle\Features\Context\FeatureContext
{
    /**
     * Returns the data directory for test extras.
     *
     * @return string
     */
    protected function getDataDir()
    {
        return __DIR__ . '/../data/';
    }
}
