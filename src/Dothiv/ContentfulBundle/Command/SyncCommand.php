<?php

namespace Dothiv\ContentfulBundle\Command;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Dothiv\ContentfulBundle\Adapter\HttpClientAdapter;
use Dothiv\ContentfulBundle\Client\HttpClient;
use Dothiv\ContentfulBundle\Logger\OutputInterfaceLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:sync')
            ->setDescription('Sync entries from a space')
            ->addOption('space', 'S', InputOption::VALUE_REQUIRED, 'ID of the space', 'cfexampleapi')
            ->addOption('access_token', 't', InputOption::VALUE_REQUIRED, 'Access token', 'b4c0n73n7fu1')
            ->addOption('endpoint', 'p', InputOption::VALUE_OPTIONAL, 'Endpoint to sync from', 'https://cdn.contentful.com')
            ->addOption('next_sync_url', 'c', InputOption::VALUE_REQUIRED, 'Next sync url');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Cache $cache */
        /** @var EntityManager $em */
        $client  = new HttpClient($input->getOption('access_token'));
        $cache   = $this->getContainer()->get('doctrine_cache.providers.contentful_api_cache');
        $em      = $this->getContainer()->get('doctrine.orm.entity_manager');
        $adapter = new HttpClientAdapter(
            $input->getOption('space'),
            $client,
            $this->getContainer()->get('event_dispatcher')
        );
        $adapter->setEndpoint($input->getOption('endpoint'));
        $adapter->setLogger(new OutputInterfaceLogger($output));

        $nextSyncUrl = null;
        $etag        = null;
        $cacheKey    = 'sync.next_sync_url' . $input->getOption('space');
        if ($cache->contains($cacheKey)) {
            $nextSyncUrl = $cache->fetch($cacheKey);
            $etag        = $cache->fetch($cacheKey . '.etag');
        }
        $nextSyncUrlOpt = $input->getOption('next_sync_url');
        if ($nextSyncUrlOpt) {
            $nextSyncUrl = $nextSyncUrlOpt;
        }
        if (parse_url($nextSyncUrl, PHP_URL_HOST) !== parse_url($input->getOption('endpoint'), PHP_URL_HOST)) {
            // Do not continue sync from different endpoint.
            $nextSyncUrl = null;
        }
        if ($nextSyncUrl) {
            $adapter->setNextSyncUrl($nextSyncUrl);
            $client->setEtag($etag);
        }
        $nextSyncUrl = $adapter->sync();
        $em->flush();
        $cache->save($cacheKey, $nextSyncUrl);
        $cache->save($cacheKey . '.etag', $client->header('etag'));
    }
}
