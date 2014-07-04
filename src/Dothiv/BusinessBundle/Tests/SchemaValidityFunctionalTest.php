<?php

namespace Dothiv\BusinessBundle\Tests;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests the validity of the schema.
 */
class SchemaValidityFunctionalTest extends WebTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $magentoEm;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        /** @var ManagerRegistry $doctrine */
        $doctrine = static::$kernel->getContainer()
            ->get('doctrine');
        $this->em = $doctrine->getManager();
    }

    /**
     * @test
     */
    public function theSchemaShouldBeValid()
    {
        $validator = new SchemaValidator($this->em);
        $this->assertSame(array(), $validator->validateMapping(), 'Schema has valid mappings');
        // This could be used to create the schema
        // $schemaTool = new SchemaTool($em);
        // $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->clear();
    }
}
