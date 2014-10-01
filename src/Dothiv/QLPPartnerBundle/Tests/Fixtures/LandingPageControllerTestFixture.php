<?php

namespace Dothiv\QLPPartnerBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;

class LandingPageControllerTestFixture implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $now = new \DateTime();

        $qlpContentType = new ContentfulContentType();
        $qlpContentType->setId('QLP');
        $qlpContentType->setRevision(1);
        $qlpContentType->setDisplayField('domain');
        $qlpContentType->setSpaceId('charity_space');
        $qlpContentType->setName('QLP');
        $qlpContentType->setCreatedAt($now);
        $qlpContentType->setUpdatedAt($now);
        $manager->persist($qlpContentType);

        $thjnk = new ContentfulEntry();
        $thjnk->setId('a');
        $thjnk->setRevision(1);
        $thjnk->setContentTypeId($qlpContentType->getName());
        $thjnk->setName('thjnk');
        $thjnk->setSpaceId('charity_space');
        $thjnk->setCreatedAt($now);
        $thjnk->setUpdatedAt($now);
        $thjnk->domain        = array('en' => 'thjnk.hiv');
        $thjnk->partnerDomain = array('en' => 'thjnk.de');
        $manager->persist($thjnk);

        $moniker = new ContentfulEntry();
        $moniker->setId('b');
        $moniker->setRevision(1);
        $moniker->setContentTypeId($qlpContentType->getName());
        $moniker->setName('moniker');
        $moniker->setSpaceId('charity_space');
        $moniker->setCreatedAt($now);
        $moniker->setUpdatedAt($now);
        $moniker->domain        = array('en' => 'moniker.hiv');
        $moniker->partnerDomain = array('en' => 'moniker.com');
        $moniker->partnerPath   = array('en' => '/some/path');
        $manager->persist($moniker);

        $manager->flush();
    }

} 
