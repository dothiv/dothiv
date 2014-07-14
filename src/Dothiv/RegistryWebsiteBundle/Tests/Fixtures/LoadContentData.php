<?php

namespace Dothiv\RegistryWebsiteBundle\Tests\Fixtures;

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
        $stringContentType->setSpaceId('registry_space');
        $stringContentType->setName('String');
        $stringContentType->setCreatedAt($now);
        $stringContentType->setUpdatedAt($now);
        $manager->persist($stringContentType);
        
        $emailContentType = new ContentfulContentType();
        $emailContentType->setId('eMail');
        $emailContentType->setRevision(1);
        $emailContentType->setDisplayField('code');
        $emailContentType->setSpaceId('registry_space');
        $emailContentType->setName('eMail');
        $emailContentType->setCreatedAt($now);
        $emailContentType->setUpdatedAt($now);
        $manager->persist($emailContentType);

        $loginTemplate = new ContentfulEntry();
        $loginTemplate->setId('c');
        $loginTemplate->setRevision(1);
        $loginTemplate->setContentTypeId($emailContentType->getName());
        $loginTemplate->setName('login');
        $loginTemplate->setSpaceId('registry_space');
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
