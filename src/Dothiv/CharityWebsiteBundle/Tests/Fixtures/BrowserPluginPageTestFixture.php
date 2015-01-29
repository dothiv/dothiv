<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;

class BrowserPluginPageTestFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $now = new \DateTime();

        $pageContentType = new ContentfulContentType();
        $pageContentType->setId('Page');
        $pageContentType->setRevision(1);
        $pageContentType->setDisplayField('code');
        $pageContentType->setSpaceId('charity_space');
        $pageContentType->setName('Page');
        $pageContentType->setCreatedAt($now);
        $pageContentType->setUpdatedAt($now);
        $manager->persist($pageContentType);

        $blockContentType = new ContentfulContentType();
        $blockContentType->setId('Block');
        $blockContentType->setRevision(1);
        $blockContentType->setDisplayField('code');
        $blockContentType->setSpaceId('charity_space');
        $blockContentType->setName('Block');
        $blockContentType->setCreatedAt($now);
        $blockContentType->setUpdatedAt($now);
        $manager->persist($blockContentType);

        $redirectsBlock = new ContentfulEntry();
        $redirectsBlock->setId('c');
        $redirectsBlock->setRevision(1);
        $redirectsBlock->setContentTypeId($blockContentType->getName());
        $redirectsBlock->setName('redirect');
        $redirectsBlock->setSpaceId('charity_space');
        $redirectsBlock->setCreatedAt($now);
        $redirectsBlock->setUpdatedAt($now);
        $redirectsBlock->title = ['en' => "title"];
        $redirectsBlock->text = ['en' => "text"];
        $redirectsBlock->code  = ['en' => 'redirect'];
        $manager->persist($redirectsBlock);

        $pageEntry = new ContentfulEntry();
        $pageEntry->setId('b');
        $pageEntry->setRevision(1);
        $pageEntry->setContentTypeId($pageContentType->getName());
        $pageEntry->setName('browserplugin');
        $pageEntry->setSpaceId('charity_space');
        $pageEntry->setCreatedAt($now);
        $pageEntry->setUpdatedAt($now);
        $pageEntry->title  = ['en' => "title"];
        $children          = [
            'en' => [
                'sys' => [
                    'type'     => 'Link',
                    'linkType' => 'Entry',
                    'id'       => 'c'
                ]
            ]
        ];
        $pageEntry->blocks = ['en' => $children];
        $manager->persist($pageEntry);

        $registrar = new Registrar();
        $registrar->setExtId('1234-AB');
        $manager->persist($registrar);

        $clickCounter = new Banner();
        $clickCounter->setRedirectUrl('http://example.com/');
        $manager->persist($clickCounter);

        $domain = new Domain();
        $domain->setName('example.hiv');
        $domain->setActiveBanner($clickCounter);
        $domain->setRegistrar($registrar);
        $domain->setOwnerEmail('john.doe@example.hiv');
        $domain->setOwnerName('John Doe');
        $manager->persist($domain);

        $manager->flush();
    }
}
