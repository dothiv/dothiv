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

        $stringContentType = new ContentfulContentType();
        $stringContentType->setId('String');
        $stringContentType->setRevision(1);
        $stringContentType->setDisplayField('code');
        $stringContentType->setSpaceId('charity_space');
        $stringContentType->setName('String');
        $stringContentType->setCreatedAt($now);
        $stringContentType->setUpdatedAt($now);
        $manager->persist($stringContentType);
        
        $emailContentType = new ContentfulContentType();
        $emailContentType->setId('eMail');
        $emailContentType->setRevision(1);
        $emailContentType->setDisplayField('code');
        $emailContentType->setSpaceId('charity_space');
        $emailContentType->setName('eMail');
        $emailContentType->setCreatedAt($now);
        $emailContentType->setUpdatedAt($now);
        $manager->persist($emailContentType);

        $configureTemplate = new ContentfulEntry();
        $configureTemplate->setId('b');
        $configureTemplate->setRevision(1);
        $configureTemplate->setContentTypeId($emailContentType->getName());
        $configureTemplate->setName('domain.registered');
        $configureTemplate->setSpaceId('charity_space');
        $configureTemplate->setCreatedAt($now);
        $configureTemplate->setUpdatedAt($now);
        $configureTemplate->code     = array('en' => "domain.registered");
        $configureTemplate->subject  = array('en' => "{{ domainName }}");
        $configureTemplate->text     = array('en' => "{{ ownerName }} {{ loginLink }} {{ claimToken }}");
        $configureTemplate->html     = array('en' => "{{ ownerName }} {{ loginLink }} {{ claimToken }}");
        $configureTemplate->htmlHead = array('en' => "");
        $configureTemplate->htmlFoot = array('en' => "");
        $manager->persist($configureTemplate);

        $loginTemplate = new ContentfulEntry();
        $loginTemplate->setId('c');
        $loginTemplate->setRevision(1);
        $loginTemplate->setContentTypeId($emailContentType->getName());
        $loginTemplate->setName('login');
        $loginTemplate->setSpaceId('charity_space');
        $loginTemplate->setCreatedAt($now);
        $loginTemplate->setUpdatedAt($now);
        $loginTemplate->code     = array('en' => "login");
        $loginTemplate->subject  = array('en' => "Login");
        $loginTemplate->text     = array('en' => "{{ loginLink }}");
        $loginTemplate->html     = array('en' => "{{ loginLink }}");
        $loginTemplate->htmlHead = array('en' => "");
        $loginTemplate->htmlFoot = array('en' => "");
        $manager->persist($loginTemplate);

        $manager->flush();
    }
}
