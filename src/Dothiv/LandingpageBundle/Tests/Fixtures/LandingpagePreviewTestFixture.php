<?php


namespace Dothiv\LandingpageBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
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

        $this->createDomain($manager, $registrar, 'caro4life.hiv', 'john.doe@example.com', 'us3rh4ndl3');
        $this->createDomain($manager, $registrar, 'polly4life.hiv', 'mike.miller@example.com', 'm1k3sh4ndl3');

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

    /**
     * @param ObjectManager $manager
     * @param Registrar     $registrar
     * @param string        $domainName
     * @param string        $email
     * @param string        $handle
     */
    protected function createDomain(ObjectManager $manager, Registrar $registrar, $domainName, $email, $handle)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $user->setHandle($handle);
        $manager->persist($user);

        $domain = new Domain();
        $domain->setOwner($user);
        $domain->setName($domainName);
        $domain->setRegistrar($registrar);
        $manager->persist($domain);

        $userToken = new UserToken();
        $userToken->setUser($user);
        $userToken->setToken('usert0k3n');
        $userToken->setScope(new IdentValue('login'));
        $lifetTime = new \DateTime();
        $userToken->setLifeTime($lifetTime->modify('+1 day'));
        $manager->persist($userToken);

        $landingpageConfig = new LandingpageConfiguration();
        $landingpageConfig->setDomain($domain);
        $landingpageConfig->setName('Caro');
        $landingpageConfig->setLanguage(new IdentValue('en'));
        $manager->persist($landingpageConfig);
    }
}
