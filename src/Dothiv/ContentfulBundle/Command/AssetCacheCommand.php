<?php

namespace Dothiv\ContentfulBundle\Command;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Dothiv\ContentfulBundle\Adapter\HttpClientAdapter;
use Dothiv\ContentfulBundle\Exception\RuntimeException;
use Dothiv\ContentfulBundle\Logger\OutputInterfaceLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AssetCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:assets:cache')
            ->setDescription('Cache assets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $adapter \Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapter */
        /** @var $assetRepo \Dothiv\ContentfulBundle\Repository\ContentfulAssetRepository */
        $adapter   = $this->getContainer()->get('dothiv_contentful.asset');
        $assetRepo = $this->getContainer()->get('dothiv_contentful.repo.asset');
        $adapter->setLogger(new OutputInterfaceLogger($output));
        foreach ($assetRepo->findAll() as $asset) {
            $adapter->cache($asset);
        }
    }
} 
