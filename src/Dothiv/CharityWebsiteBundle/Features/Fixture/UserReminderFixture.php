<?php


namespace Dothiv\CharityWebsiteBundle\Features\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\W3CDateTimeValue;
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

        $proof = new Attachment();
        $proof->setUser($user);
        $proof->setHandle('ad54af9f3a2e137d04588712e3d98e0d');
        $proof->setMimeType('application/pdf');
        $proof->setExtension('pdf');
        $manager->persist($proof);

        $nonProfitRegisteredNotOnlineRegistration = new NonProfitRegistration();
        $nonProfitRegisteredNotOnlineRegistration->setUser($user);
        $nonProfitRegisteredNotOnlineRegistration->setDomain('non-profit-registered-not-online.hiv');
        $nonProfitRegisteredNotOnlineRegistration->setPersonFirstname('Jill');
        $nonProfitRegisteredNotOnlineRegistration->setPersonSurname('Jones');
        $nonProfitRegisteredNotOnlineRegistration->setPersonEmail('jill@example.com');
        $nonProfitRegisteredNotOnlineRegistration->setOrganization('ACME Inc.');
        $nonProfitRegisteredNotOnlineRegistration->setProof($proof);
        $nonProfitRegisteredNotOnlineRegistration->setAbout('ACME Stuff');
        $nonProfitRegisteredNotOnlineRegistration->setField('prevention');
        $nonProfitRegisteredNotOnlineRegistration->setPostcode('12345');
        $nonProfitRegisteredNotOnlineRegistration->setLocality('Big City');
        $nonProfitRegisteredNotOnlineRegistration->setCountry('United States');
        $nonProfitRegisteredNotOnlineRegistration->setWebsite('http://example.com/');
        $nonProfitRegisteredNotOnlineRegistration->setForward('1');
        $nonProfitRegisteredNotOnlineRegistration->setPersonPhone('+49178451');
        $nonProfitRegisteredNotOnlineRegistration->setPersonFax('+49178452');
        $nonProfitRegisteredNotOnlineRegistration->setOrgPhone('+49178453');
        $nonProfitRegisteredNotOnlineRegistration->setOrgFax('+49178454');
        $nonProfitRegisteredNotOnlineRegistration->setApproved(new W3CDateTimeValue($clock->getNow()));
        $manager->persist($nonProfitRegisteredNotOnlineRegistration);

        $nonProfitRegisteredNotOnlineDomain = new Domain();
        $nonProfitRegisteredNotOnlineDomain->setName('non-profit-registered-not-online.hiv');
        $nonProfitRegisteredNotOnlineDomain->setRegistrar($registrar);
        $nonProfitRegisteredNotOnlineDomain->setOwner($user);
        $nonProfitRegisteredNotOnlineDomain->setNonprofit(true);
        $nonProfitRegisteredNotOnlineDomain->setCreated($clock->getNow()->modify('-6 weeks'));
        $manager->persist($nonProfitRegisteredNotOnlineDomain);

        $nonProfitRegisteredNotOnlineCheck = new HivDomainCheck();
        $nonProfitRegisteredNotOnlineCheck->setDomain($nonProfitRegisteredNotOnlineDomain);
        $nonProfitRegisteredNotOnlineCheck->setUrl('http://non-profit-registered-not-online.hiv/');
        $manager->persist($nonProfitRegisteredNotOnlineCheck);

        $onlineButNotConfiguredDomain = new Domain();
        $onlineButNotConfiguredDomain->setName('online-but-not-configured.hiv');
        $onlineButNotConfiguredDomain->setRegistrar($registrar);
        $onlineButNotConfiguredDomain->setOwnerName($user->getFirstname() . ' ' . $user->getSurname());
        $onlineButNotConfiguredDomain->setOwnerEmail($user->getEmail());
        $onlineButNotConfiguredDomain->setCreated($clock->getNow()->modify('-6 weeks'));
        $manager->persist($onlineButNotConfiguredDomain);

        $onlineButNotConfiguredDomainCheck = new HivDomainCheck();
        $onlineButNotConfiguredDomainCheck->setDomain($onlineButNotConfiguredDomain);
        $onlineButNotConfiguredDomainCheck->setUrl('http://online-but-not-configured.hiv/');
        $onlineButNotConfiguredDomainCheck->setDnsOk(true);
        $manager->persist($onlineButNotConfiguredDomainCheck);

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
