<?php

namespace Dothiv\APIBundle\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\CommonContexts\DoctrineFixturesContext;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Tools\SchemaTool;
use Sanpi\Behatch\Context\BehatchContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * Returns the data directory for test extras.
     *
     * @return string
     */
    protected function getDataDir()
    {
        return __DIR__ . '/../data/';
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
        if (method_exists($entity, 'setCreated')) {
            $entity->setCreated(new \DateTime());
        }
        foreach ($table->getRowsHash() as $k => $v) {
            $setter = 'set' . ucfirst($k);
            $entity->$setter($this->getValue($v));
        }
        $em->persist($entity);
        $em->flush();
        $this->store($storageName, $entity);
    }

    /**
     * Updates an entity.
     *
     * @Given /^I update the "(?P<storageName>[^"]*)" entity with values:$/
     */
    public function iUpdateTheEntityWithValues($storageName, TableNode $table)
    {
        $entity = $this->getValue('{' . $storageName . '}');
        foreach ($table->getRowsHash() as $k => $v) {
            $setter = 'set' . ucfirst($k);
            $entity->$setter($this->getValue($v));
        }
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Calls the method of a named service and stores the result.
     *
     * @Given /^"(?P<storageName>[^"]*)" contains the result of calling "(?P<methodName>[^"]+)" on the "(?P<serviceId>[^"]+)" service with values:$/
     */
    public function theResultOfCallingWithIsStoredIn($serviceId, $methodName, $storageName, TableNode $table)
    {
        $service = $this->kernel->getContainer()->get($serviceId);
        $args    = array();
        foreach ($table->getRow(0) as $positionalArg) {
            $args[] = $this->getValue($positionalArg);
        }
        $result = call_user_func_array(array($service, $methodName), $args);
        $this->store($storageName, $result);
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
            if (preg_match('/^(\\\\[\\\\A-Za-z]+)+@(.+)/', $match[1], $classMatch)) {
                $class = $classMatch[1];
                return new $class($classMatch[2]);
            }
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
     * @Given /^I send a (?P<method>[A-Z]+) request to "(?P<url>[^"]*)" with JSON values:$/
     */
    public function iSendARequestToWithValues($method, $url, TableNode $table)
    {
        $client     = $this->getSubcontext('mink')->getSession()->getDriver()->getClient();
        $parameters = $table->getRowsHash();
        $client->request(
            $method,
            $this->getSubcontext('rest')->locatePath($url),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($parameters)
        );
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

    /**
     * @Given /^I send a (?P<method>[A-Z]+) request to "(?P<url>[^"]*)" with file "(?P<filename>[^"]*)" as "(?P<fileparam>[^"]*)"$/
     * @Given /^I send a (?P<method>[A-Z]+) request to "(?P<url>[^"]*)" with file "(?P<filename>[^"]*)" as "(?P<fileparam>[^"]*)" and parameters:$/
     */
    public function iSendAUploadRequest($method, $url, $filename, $fileparam, TableNode $table = null)
    {
        $client = $this->getSubcontext('mink')->getSession()->getDriver()->getClient();

        // intercept redirection
        $client->followRedirects(false);

        // Copy original
        $originalFile = $this->getDataDir() . '/' . $filename;
        $tempFile     = tempnam(sys_get_temp_dir(), 'behat-data-');
        copy($originalFile, $tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            basename($tempFile),
            mime_content_type($tempFile),
            filesize($tempFile)
        );

        $files = array(
            $fileparam => $uploadedFile
        );

        $parameters = array();
        if ($table !== null) {
            $parameters = $table->getRowsHash();
        }
        $client->request($method, $url, $parameters, $files);
        $client->followRedirects(true);
    }

    /**
     * @Given /^the JSON node "(?P<name>[^"]*)" should not be empty$/
     */
    public function theJsonNodeShouldNotBeEmpty($name)
    {
        $json = $this->getJson();
        \PHPUnit_Framework_Assert::assertObjectHasAttribute($name, $json);
        \PHPUnit_Framework_Assert::assertNotEmpty($json->$name);
    }

    /**
     * @Given /^the header "(?P<name>[^"]*)" should exist$/
     *
     * @param string $name
     *
     * @return mixed
     */
    public function theHeaderShouldExist($name)
    {
        $headers = $this->getSubcontext('mink')->getSession()->getResponseHeaders();
        \PHPUnit_Framework_Assert::assertArrayHasKey(strtolower($name), $headers);
        return $headers[strtolower($name)];
    }

    /**
     * @Given /^the header "(?P<header>[^"]*)" is stored in "(?P<store>[^"]*)"$/
     */
    public function theHeaderIsStoredIn($header, $store)
    {
        $val = $this->theHeaderShouldExist($header);
        $this->store($store, join("\n", $val));
    }

    /**
     * @Then /^the image should be (?P<width>\d+)x(?P<height>\d+)$/
     */
    public function theImageShouldBeBy($width, $height)
    {
        $imageData = $this->getSubcontext('mink')->getSession()->getPage()->getContent();

        $imagesize    = getimagesize(
            sprintf('data://%s;base64,%s', join("\n", $this->theHeaderShouldExist('content-type')), base64_encode($imageData))
        );
        $expectedSize = sprintf("%dx%d", $width, $height);
        $actualSize   = sprintf("%dx%d", $imagesize[0], $imagesize[1]);
        if ($actualSize !== $expectedSize) {
            throw new \Exception(sprintf("Size of image is %s where %s was expected.", $actualSize, $expectedSize));
        }
    }

    /**
     * Sends a HTTP request to a stored URL
     *
     * @Given /^I send a (?P<method>[A-Z]+) request to \{(?P<url>[^"]*)\}$/
     * @Given /^I send a (?P<method>[A-Z]+) request on \{(?P<url>[^"]*)\}$/
     */
    public function iSendARequestTo($method, $url)
    {
        $this->getSubcontext('rest')->iSendARequestTo($method, $this->getValue('{' . $url . '}'));
    }

    /**
     * @Given /^the JSON node "(?P<node>[^"]*)" should contain \{(?P<storageName>[^\}]*)\}$/
     */
    public function theJsonNodeShouldBeEqualToExtrasvisualurl($node, $storageName)
    {
        $val = $this->getValue('{' . $storageName . '}');
        $this->getSubcontext('json')->theJsonNodeShouldBeEqualTo($node, (string)$val);
    }

    /**
     * @Given /^the JSON object should be a list with at least (?P<num>\d+) element$/
     */
    public function theJsonObjectShouldBeAListWithAtLeastElement($num)
    {
        $json = (array)$this->getJson();
        \PHPUnit_Framework_Assert::assertTrue(count($json) >= $num, sprintf('List does not have at least %d elements!', $num));
    }

    /**
     * @Given /^the JSON node "(?P<node>[^"]*)" should contain at least (?P<num>\d+) element$/
     */
    public function theJsonNodeShouldContainAtLeastElement($node, $num)
    {
        $json = $this->getJson();
        \PHPUnit_Framework_Assert::assertObjectHasAttribute($node, $json);
        \PHPUnit_Framework_Assert::assertTrue(count($json->$node) >= $num, sprintf('List does not have at least %d elements!', $num));
    }
}
