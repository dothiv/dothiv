<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;

class LoadContentData implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $now         = new \DateTime();
        $contentType = new ContentfulContentType();
        $contentType->setId('a');
        $contentType->setRevision(1);
        $contentType->setDisplayField('code');
        $contentType->setSpaceId('charity_space');
        $contentType->setName('eMail');
        $contentType->setCreatedAt($now);
        $contentType->setUpdatedAt($now);
        $manager->persist($contentType);

        $loginTemplate = new ContentfulEntry();
        $loginTemplate->setRevision(1);
        $loginTemplate->setId('b');
        $loginTemplate->setContentTypeId($contentType->getName());
        $loginTemplate->setName('domain.registered');
        $loginTemplate->setSpaceId('charity_space');
        $loginTemplate->setCreatedAt($now);
        $loginTemplate->setUpdatedAt($now);
        $loginTemplate->code     = array('en' => "domain.registered");
        $loginTemplate->subject  = array('en' => "Configure your .hiv domain {{ domainName }}");
        $loginTemplate->text     = array('en' => "{{ ownerName }} {{ loginLink }} {{ claimToken }}");
        $loginTemplate->html     = array('en' => "{{ ownerName }} {{ loginLink }} {{ claimToken }}");
        $loginTemplate->htmlHead = array('en' => "");
        $loginTemplate->htmlFoot = array('en' => "");

        $manager->persist($loginTemplate);
        $manager->flush();
    }
}
