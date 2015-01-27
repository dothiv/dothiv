<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Dothiv\AfiliasImporterBundle\AfiliasImporterBundleEvents;
use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\AfiliasImporterBundle\Service\AfiliasImporterService;
use Dothiv\AfiliasImporterBundle\Service\AfiliasImporterServiceInterface;
use Dothiv\ValueObject\URLValue;
use Guzzle\Http\Client;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Fetch deleted domains since …
 */
class Version20150127155151 extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql('CREATE TABLE DeletedDomain (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) NOT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        /** @var AfiliasImporterServiceInterface $service */
        $client     = new Client();
        $dispatcher = new EventDispatcher();

        $deletedDomains = [];
        $dispatcher->addListener(AfiliasImporterBundleEvents::DOMAIN_DELETED, function (DomainTransactionEvent $event) use (&$deletedDomains) {
            $deletedDomains[] = [$event->ObjectName, $event->TransactionDate];
        });

        $service    = new AfiliasImporterService($client, $dispatcher);
        $url        = $this->container->getParameter('dothiv_afilias_importer.service_url') . 'transactions';
        $currentUrl = $url;
        do {
            $nextUrl = $service->fetchTransactions(new URLValue($currentUrl));
        } while ($nextUrl != $currentUrl && $currentUrl = $nextUrl);

        foreach ($deletedDomains as $deletedDomain) {
            $this->addSql('INSERT INTO DeletedDomain (domain, created) VALUES(?, ?)', $deletedDomain);
        }
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE DeletedDomain');
    }
}
