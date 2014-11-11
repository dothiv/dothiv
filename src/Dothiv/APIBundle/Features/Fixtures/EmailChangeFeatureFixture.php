<?php

namespace Dothiv\APIBundle\Features\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;

class EmailChangeFeatureFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $now = new \DateTime();

        $emailContentType = new ContentfulContentType();
        $emailContentType->setId('eMail');
        $emailContentType->setRevision(1);
        $emailContentType->setDisplayField('code');
        $emailContentType->setSpaceId('charity_space');
        $emailContentType->setName('eMail');
        $emailContentType->setCreatedAt($now);
        $emailContentType->setUpdatedAt($now);
        $manager->persist($emailContentType);

        $domainRegisteredEmail = new ContentfulEntry();
        $domainRegisteredEmail->setId('profile.change.confirm');
        $domainRegisteredEmail->setRevision(1);
        $domainRegisteredEmail->setContentTypeId($emailContentType->getName());
        $domainRegisteredEmail->setName('profile.change.confirm');
        $domainRegisteredEmail->setSpaceId('charity_space');
        $domainRegisteredEmail->setCreatedAt($now);
        $domainRegisteredEmail->setUpdatedAt($now);
        $domainRegisteredEmail->subject = array('en' => 'Subject');
        $domainRegisteredEmail->text    = array('en' => 'Text Body');
        $manager->persist($domainRegisteredEmail);

        $manager->flush();
    }

}
