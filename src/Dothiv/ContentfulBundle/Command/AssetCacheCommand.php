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
        $totalSize = 0;
        foreach ($assetRepo->findAll() as $asset) {
            foreach ($asset->file as $locale => $file) {
                $localFile = $adapter->getLocalFile($asset->getId(), $locale);
                if ($localFile->isFile()) {
                    continue;
                }
                $output->writeln(
                    sprintf(
                        'Caching "%s" file for asset "%s" as "%s" ...',
                        $locale,
                        $asset->getId(),
                        $localFile->getPathname()
                    )
                );
                $dir = new \SplFileInfo($localFile->getPath());
                if (!$dir->isWritable()) {
                    throw new RuntimeException(
                        sprintf(
                            'Target directory "%s" is not writeable!',
                            $localFile->getPath()
                        )
                    );
                    exit(1);
                }
                copy(str_replace('//', 'https://', $file['url']), $localFile->getPathname());
                $size = filesize($localFile->getPathname());
                $output->writeln(
                    sprintf(
                        '%d bytes saved.',
                        $size
                    )
                );
                $totalSize += $size;
            }
        }
        $output->writeln(
            sprintf(
                '%d total bytes saved.',
                $totalSize
            )
        );
    }
} 
