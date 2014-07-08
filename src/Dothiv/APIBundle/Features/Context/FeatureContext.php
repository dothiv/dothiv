<?php

namespace Dothiv\APIBundle\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\CommonContexts\DoctrineFixturesContext;
use Behat\CommonContexts\SymfonyDoctrineContext;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Tools\SchemaTool;
use Sanpi\Behatch\Context\BehatchContext;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext extends BehatContext
    implements KernelAwareInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    private $parameters;

    /**
     * @var ArrayCollection
     */
    private $storage;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('behatch', new BehatchContext($parameters));
        $this->useContext('mink', new MinkContext($parameters));
        $this->useContext('doctrine_fixtures_context', new DoctrineFixturesContext());
        $this->storage = new ArrayCollection();
    }

    /**
     * Clear DB before each scenario
     *
     * @BeforeScenario
     */
    public function clearDb(ScenarioEvent $event)
    {
        $entityManager = $this->getEntityManager();
        $metadata      = $entityManager->getMetadataFactory()->getAllMetadata();
        $tool          = new SchemaTool($entityManager);
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @Given /^the "(?P<entityName>[^"]*)" entity exists in "(?P<storageName>[^"]*)" with values:$/
     */
    public function theEntityExistsInWithValues($entityName, $storageName, TableNode $table)
    {
        $em         = $this->getEntityManager();
        $entityInfo = $em->getClassMetadata($entityName);
        $entity     = new $entityInfo->name;
        foreach ($table->getRowsHash() as $k => $v) {
            $setter = 'set' . ucfirst($k);
            $entity->$setter($this->getValue($v));
        }
        $em->persist($entity);
        $em->flush();
        $this->store($storageName, $entity);
    }

    /**
     * Returns a value with replaced placeholders for storage objects.
     *
     * @param $value
     *
     * @return mixed
     */
    protected function getValue($value)
    {
        preg_match_all('/\{([^\}]+)\}/', $value, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            return $this->storage->get($match[1]);
        }
        return $value;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws \InvalidArgumentException If element already exists.
     */
    protected function store($name, $value)
    {
        if ($this->storage->containsKey($name)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'There is already an element stored at "%s"!',
                    $name
                )
            );
        }
        $this->storage->set($name, $value);
    }

    /**
     * @Given /^I send a (?P<method>[A-Z]+) request to "(?P<url>[^"]*)" with values:$/
     */
    public function iSendARequestToWithValues($method, $url, TableNode $table)
    {
        $client     = $this->getSubcontext('mink')->getSession()->getDriver()->getClient();
        $parameters = $table->getRowsHash();
        $client->request($method, $this->getSubcontext('rest')->locatePath($url), $parameters);
    }

    /**
     * Add an bearer token header element in a request
     *
     * @Then /^I add Bearer token equal to "(?P<token>[^"]*)"$/
     */
    public function iAddHeaderEqualTo($token)
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
    }

    /**
     * @Given /^the JSON object should be a list with (?P<nth>\d+) elements?$/
     */
    public function theJsonObjectShouldBeAListWithElement($nth)
    {
        $json = $this->getJson();
        \PHPUnit_Framework_Assert::assertInternalType('array', $json);
        \PHPUnit_Framework_Assert::assertEquals(intval($nth), count($json));
    }

    /**
     * @return \stdClass|array
     */
    private function getJson()
    {
        $content = $this->getSubcontext('mink')->getSession()->getPage()->getContent();
        return json_decode($content);
    }

    /**
     * @Given /^"(?P<key>[^"]*)" on the JSON list (?P<index>\d+) should be "(?P<expected>[^"]*)"$/
     */
    public function onTheJsonListShouldBe($key, $expected, $index)
    {
        $json = $this->getJson();
        \PHPUnit_Framework_Assert::assertEquals($json[$index]->$key, $expected);
    }
}
