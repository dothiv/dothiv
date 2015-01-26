<?php


namespace Dothiv\CharityWebsiteBundle\Features\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\Banner;
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

        $nonProfitRegisteredNotRegistered = new NonProfitRegistration();
        $nonProfitRegisteredNotRegistered->setUser($user);
        $nonProfitRegisteredNotRegistered->setDomain('non-profit-registered-not-registered.hiv');
        $nonProfitRegisteredNotRegistered->setPersonFirstname('Jill');
        $nonProfitRegisteredNotRegistered->setPersonSurname('Jones');
        $nonProfitRegisteredNotRegistered->setPersonEmail('jill@example.com');
        $nonProfitRegisteredNotRegistered->setOrganization('ACME Inc.');
        $nonProfitRegisteredNotRegistered->setProof($proof);
        $nonProfitRegisteredNotRegistered->setAbout('ACME Stuff');
        $nonProfitRegisteredNotRegistered->setField('prevention');
        $nonProfitRegisteredNotRegistered->setPostcode('12345');
        $nonProfitRegisteredNotRegistered->setLocality('Big City');
        $nonProfitRegisteredNotRegistered->setCountry('United States');
        $nonProfitRegisteredNotRegistered->setWebsite('http://example.com/');
        $nonProfitRegisteredNotRegistered->setForward('1');
        $nonProfitRegisteredNotRegistered->setPersonPhone('+49178451');
        $nonProfitRegisteredNotRegistered->setPersonFax('+49178452');
        $nonProfitRegisteredNotRegistered->setOrgPhone('+49178453');
        $nonProfitRegisteredNotRegistered->setOrgFax('+49178454');
        $nonProfitRegisteredNotRegistered->setApproved(new W3CDateTimeValue($clock->getNow()));
        $manager->persist($nonProfitRegisteredNotRegistered);

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

        $forProfitOnlineButNotConfiguredDomain = new Domain();
        $forProfitOnlineButNotConfiguredDomain->setName('for-profit-online-but-not-configured.hiv');
        $forProfitOnlineButNotConfiguredDomain->setRegistrar($registrar);
        $forProfitOnlineButNotConfiguredDomain->setOwnerName($user->getFirstname() . ' ' . $user->getSurname());
        $forProfitOnlineButNotConfiguredDomain->setOwnerEmail($user->getEmail());
        $forProfitOnlineButNotConfiguredDomain->setCreated($clock->getNow()->modify('-3 weeks'));
        $manager->persist($forProfitOnlineButNotConfiguredDomain);

        $forProfitOnlineButNotConfiguredDomainCheck = new HivDomainCheck();
        $forProfitOnlineButNotConfiguredDomainCheck->setDomain($forProfitOnlineButNotConfiguredDomain);
        $forProfitOnlineButNotConfiguredDomainCheck->setUrl('http://for-profit-online-but-not-configured.hiv/');
        $forProfitOnlineButNotConfiguredDomainCheck->setDnsOk(true);
        $manager->persist($forProfitOnlineButNotConfiguredDomainCheck);

        $nonProfitOnlineButNotConfiguredDomain = new Domain();
        $nonProfitOnlineButNotConfiguredDomain->setName('non-profit-online-but-not-configured.hiv');
        $nonProfitOnlineButNotConfiguredDomain->setRegistrar($registrar);
        $nonProfitOnlineButNotConfiguredDomain->setOwnerName($user->getFirstname() . ' ' . $user->getSurname());
        $nonProfitOnlineButNotConfiguredDomain->setOwnerEmail($user->getEmail());
        $nonProfitOnlineButNotConfiguredDomain->setCreated($clock->getNow()->modify('-5 weeks'));
        $nonProfitOnlineButNotConfiguredDomain->setNonprofit(true);
        $manager->persist($nonProfitOnlineButNotConfiguredDomain);

        $nonProfitOnlineButNotConfiguredDomainCheck = new HivDomainCheck();
        $nonProfitOnlineButNotConfiguredDomainCheck->setDomain($nonProfitOnlineButNotConfiguredDomain);
        $nonProfitOnlineButNotConfiguredDomainCheck->setUrl('http://non-profit-online-but-not-configured.hiv/');
        $nonProfitOnlineButNotConfiguredDomainCheck->setDnsOk(true);
        $manager->persist($nonProfitOnlineButNotConfiguredDomainCheck);

        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain = new Domain();
        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain->setName('non-profit-online-configured-click-counter-but-not-installed.hiv');
        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain->setRegistrar($registrar);
        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain->setOwner($user);
        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain->setOwnerName($user->getFirstname() . ' ' . $user->getSurname());
        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain->setOwnerEmail($user->getEmail());
        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain->setCreated($clock->getNow()->modify('-5 weeks'));
        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain->setNonprofit(true);
        $manager->persist($nonProfitOnlineClickCounterConfiguredButNotInstalledDomain);

        $nonProfitOnlineClickCounterConfiguredButNotInstalledBanner = new Banner();
        $nonProfitOnlineClickCounterConfiguredButNotInstalledBanner->setCreated($clock->getNow()->modify('-5 weeks'));
        $manager->persist($nonProfitOnlineClickCounterConfiguredButNotInstalledBanner);
        $nonProfitOnlineClickCounterConfiguredButNotInstalledDomain->setActiveBanner($nonProfitOnlineClickCounterConfiguredButNotInstalledBanner);

        $nonProfitOnlineClickCounterConfiguredButNotInstalledCheck = new HivDomainCheck();
        $nonProfitOnlineClickCounterConfiguredButNotInstalledCheck->setDomain($nonProfitOnlineClickCounterConfiguredButNotInstalledDomain);
        $nonProfitOnlineClickCounterConfiguredButNotInstalledCheck->setUrl('http://non-profit-online-configured-click-counter-but-not-installed/');
        $nonProfitOnlineClickCounterConfiguredButNotInstalledCheck->setDnsOk(true);
        $nonProfitOnlineClickCounterConfiguredButNotInstalledCheck->setStatusCode(200);
        $manager->persist($nonProfitOnlineClickCounterConfiguredButNotInstalledCheck);

        $forProfitOnlineClickCounterConfiguredButNotInstalledDomain = new Domain();
        $forProfitOnlineClickCounterConfiguredButNotInstalledDomain->setName('for-profit-online-configured-click-counter-but-not-installed.hiv');
        $forProfitOnlineClickCounterConfiguredButNotInstalledDomain->setRegistrar($registrar);
        $forProfitOnlineClickCounterConfiguredButNotInstalledDomain->setOwner($user);
        $forProfitOnlineClickCounterConfiguredButNotInstalledDomain->setOwnerName($user->getFirstname() . ' ' . $user->getSurname());
        $forProfitOnlineClickCounterConfiguredButNotInstalledDomain->setOwnerEmail($user->getEmail());
        $forProfitOnlineClickCounterConfiguredButNotInstalledDomain->setCreated($clock->getNow()->modify('-5 weeks'));
        $manager->persist($forProfitOnlineClickCounterConfiguredButNotInstalledDomain);

        $forProfitOnlineClickCounterConfiguredButNotInstalledBanner = new Banner();
        $forProfitOnlineClickCounterConfiguredButNotInstalledBanner->setCreated($clock->getNow()->modify('-5 weeks'));
        $manager->persist($forProfitOnlineClickCounterConfiguredButNotInstalledBanner);
        $forProfitOnlineClickCounterConfiguredButNotInstalledDomain->setActiveBanner($forProfitOnlineClickCounterConfiguredButNotInstalledBanner);

        $forProfitOnlineClickCounterConfiguredButNotInstalledCheck = new HivDomainCheck();
        $forProfitOnlineClickCounterConfiguredButNotInstalledCheck->setDomain($forProfitOnlineClickCounterConfiguredButNotInstalledDomain);
        $forProfitOnlineClickCounterConfiguredButNotInstalledCheck->setUrl('http://for-profit-online-configured-click-counter-but-not-installed/');
        $forProfitOnlineClickCounterConfiguredButNotInstalledCheck->setDnsOk(true);
        $forProfitOnlineClickCounterConfiguredButNotInstalledCheck->setStatusCode(200);
        $manager->persist($forProfitOnlineClickCounterConfiguredButNotInstalledCheck);

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
