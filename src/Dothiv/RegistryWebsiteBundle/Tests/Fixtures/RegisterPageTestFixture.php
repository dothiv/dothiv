<?php


namespace Dothiv\RegistryWebsiteBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;

class RegisterPageTestFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $now = new \DateTime();

        $config = new Config();
        $config->setName('eur_to_usd');
        $config->setValue(1.25);
        $manager->persist($config);

        $pageContentType = new ContentfulContentType();
        $pageContentType->setId('Page');
        $pageContentType->setRevision(1);
        $pageContentType->setDisplayField('code');
        $pageContentType->setSpaceId('registry_space');
        $pageContentType->setName('Page');
        $pageContentType->setCreatedAt($now);
        $pageContentType->setUpdatedAt($now);
        $manager->persist($pageContentType);

        $registerPage = new ContentfulEntry();
        $registerPage->setId('c');
        $registerPage->setRevision(1);
        $registerPage->setContentTypeId($pageContentType->getName());
        $registerPage->setName('register');
        $registerPage->setSpaceId('registry_space');
        $registerPage->setCreatedAt($now);
        $registerPage->setUpdatedAt($now);
        $registerPage->title = array('en' => 'Register');
        $manager->persist($registerPage);

        $collectionContentType = new ContentfulContentType();
        $collectionContentType->setId('Collection');
        $collectionContentType->setRevision(1);
        $collectionContentType->setDisplayField('code');
        $collectionContentType->setSpaceId('registry_space');
        $collectionContentType->setName('Collection');
        $collectionContentType->setCreatedAt($now);
        $collectionContentType->setUpdatedAt($now);
        $manager->persist($collectionContentType);

        $blockContentType = new ContentfulContentType();
        $blockContentType->setId('Block');
        $blockContentType->setRevision(1);
        $blockContentType->setDisplayField('code');
        $blockContentType->setSpaceId('registry_space');
        $blockContentType->setName('Block');
        $blockContentType->setCreatedAt($now);
        $blockContentType->setUpdatedAt($now);
        $manager->persist($blockContentType);
        
        $manager->flush();
    }

} 
