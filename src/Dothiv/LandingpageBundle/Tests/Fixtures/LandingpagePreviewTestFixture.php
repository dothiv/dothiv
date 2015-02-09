<?php


namespace Dothiv\LandingpageBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\Yaml\Yaml;

class LandingpagePreviewTestFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $registrar = new Registrar();
        $registrar->setExtId("1061-EM");
        $registrar->setName("Example Registrar");
        $manager->persist($registrar);

        $domain = new Domain();
        $domain->setName("caro4life.hiv");
        $domain->setOwnerName("Domain Administrator");
        $domain->setOwnerEmail("domain@bcme.com");
        $domain->setRegistrar($registrar);
        $manager->persist($domain);

        $landingpageConfig = new LandingpageConfiguration();
        $landingpageConfig->setDomain($domain);
        $landingpageConfig->setName('Caro');
        $landingpageConfig->setLanguage(new IdentValue('en'));
        $manager->persist($landingpageConfig);

        $now = new \DateTime();

        $stringContentType = new ContentfulContentType();
        $stringContentType->setId('String');
        $stringContentType->setRevision(1);
        $stringContentType->setDisplayField('code');
        $stringContentType->setSpaceId('landingpage_space');
        $stringContentType->setName('String');
        $stringContentType->setCreatedAt($now);
        $stringContentType->setUpdatedAt($now);
        $manager->persist($stringContentType);

        $yml     = new Yaml();
        $strings = $yml->parse(file_get_contents(__DIR__ . '/data/landingpage-preview-strings.yml'));

        foreach ($strings as $name => $fields) {
            $stringEntry = new ContentfulEntry();
            $stringEntry->setId(sha1($name));
            $stringEntry->setRevision(1);
            $stringEntry->setContentTypeId($stringContentType->getName());
            $stringEntry->setName($name);
            $stringEntry->setSpaceId('landingpage_space');
            $stringEntry->setCreatedAt($now);
            $stringEntry->setUpdatedAt($now);
            foreach ($fields as $fieldName => $field) {
                $stringEntry->$fieldName = $field;
            }
            $manager->persist($stringEntry);
        }

        $manager->flush();
    }
}
