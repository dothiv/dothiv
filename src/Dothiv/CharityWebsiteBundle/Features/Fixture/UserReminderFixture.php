<?php


namespace Dothiv\CharityWebsiteBundle\Features\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\ValueObject\ClockValue;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserReminderFixture implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        /** @var ClockValue $clock */
        $clock = $this->container->get('clock');
        $user  = new User();
        $user->setHandle('userhandle');
        $user->setEmail('someone@example.com');
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $manager->persist($user);

        $registrar = new Registrar();
        $registrar->setExtId('1234-AC');
        $registrar->setName('ACME Registrar');
        $manager->persist($registrar);

        $nonProfitLiveFewClicksDomain = new Domain();
        $nonProfitLiveFewClicksDomain->setName('non-profit-live-few-clicks.hiv');
        $nonProfitLiveFewClicksDomain->setRegistrar($registrar);
        $nonProfitLiveFewClicksDomain->setOwner($user);
        $nonProfitLiveFewClicksDomain->setNonprofit(true);
        $nonProfitLiveFewClicksDomain->enliven($clock->getNow()->modify('-5 weeks'));
        $nonProfitLiveFewClicksDomain->setClickcount(3);
        $manager->persist($nonProfitLiveFewClicksDomain);

        // Some wild domains (should not show up in reports)
        $nonProfitLiveManyClicksDomain = new Domain();
        $nonProfitLiveManyClicksDomain->setName('non-profit-live-many-clicks.hiv');
        $nonProfitLiveManyClicksDomain->setRegistrar($registrar);
        $nonProfitLiveManyClicksDomain->setOwner($user);
        $nonProfitLiveManyClicksDomain->setNonprofit(true);
        $nonProfitLiveManyClicksDomain->enliven($clock->getNow()->modify('-11 weeks'));
        $nonProfitLiveManyClicksDomain->setClickcount(10000);
        $manager->persist($nonProfitLiveManyClicksDomain);

        $profitLiveManyClicksDomain = new Domain();
        $profitLiveManyClicksDomain->setName('profit-live-many-clicks.hiv');
        $profitLiveManyClicksDomain->setRegistrar($registrar);
        $profitLiveManyClicksDomain->setOwner($user);
        $profitLiveManyClicksDomain->enliven($clock->getNow()->modify('-11 weeks'));
        $profitLiveManyClicksDomain->setClickcount(10000);
        $manager->persist($profitLiveManyClicksDomain);

        $profitLiveFewClicksDomain = new Domain();
        $profitLiveFewClicksDomain->setName('profit-live-few-clicks.hiv');
        $profitLiveFewClicksDomain->setRegistrar($registrar);
        $profitLiveFewClicksDomain->setOwner($user);
        $profitLiveFewClicksDomain->enliven($clock->getNow()->modify('-11 weeks'));
        $profitLiveFewClicksDomain->setClickcount(3);
        $manager->persist($profitLiveFewClicksDomain);

        $manager->flush();
    }
}
