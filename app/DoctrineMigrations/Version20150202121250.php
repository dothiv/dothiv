<?php

namespace Application\Migrations;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Add registry expiry date as column to domain whois table
 */
class Version20150202121250 extends AbstractMigration implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE DomainWhois ADD creationDate DATETIME NOT NULL AFTER whois, ADD expiryDate DATETIME NOT NULL AFTER creationDate');
        /** @var EntityManager $em */
        $em     = $this->container->get('doctrine.orm.default_entity_manager');
        $result = $em
            ->getRepository('DothivBusinessBundle:DomainWhois')
            ->createQueryBuilder('w')
            ->select(['w.domain', 'w.whois'])
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        foreach ($result as $row) {
            if (!$row['whois']) {
                continue;
            }
            $this->addSql('UPDATE DomainWhois SET expiryDate = ? , creationDate = ? WHERE domain = ?', [$row['whois']['Registry Expiry Date'], $row['whois']['Creation Date'], $row['domain']]);
        }
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE DomainWhois DROP creationDate');
        $this->addSql('ALTER TABLE DomainWhois DROP expiryDate');
    }
}
