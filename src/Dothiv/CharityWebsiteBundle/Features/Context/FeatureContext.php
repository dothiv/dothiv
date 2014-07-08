<?php

namespace Dothiv\CharityWebsiteBundle\Features\Context;

use Behat\Behat\Event\ScenarioEvent;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Util\Debug;

class FeatureContext extends \Dothiv\APIBundle\Features\Context\FeatureContext
{
    /**
     * Load fixtures before each scenario
     *
     * @BeforeScenario
     */
    public function clearDb(ScenarioEvent $event)
    {
        parent::clearDb($event);

        $loader = new Loader();

        $this->getMainContext()
            ->getSubcontext('doctrine_fixtures_context')
            ->loadFixtureClasses($loader, array(
                'Dothiv\CharityWebsiteBundle\Tests\Fixtures\LoadContentData'
            ));

        $em       = $this->getEntityManager();
        $purger   = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $executor->execute($loader->getFixtures(), true);
    }
}
