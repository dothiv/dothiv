<?php

namespace Dothiv\BaseWebsiteBundle\Command;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Dothiv\ContentfulBundle\Adapter\HttpClientAdapter;
use Dothiv\ContentfulBundle\Exception\RuntimeException;
use Dothiv\ContentfulBundle\Logger\OutputInterfaceLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScaleContentfulAssetsImagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:contentful:images:scale')
            ->setDescription('Generate scaled version of all contentful image assets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $assetRepo \Dothiv\ContentfulBundle\Repository\ContentfulAssetRepositoryInterface */
        /** @var $imageScaler \Dothiv\BaseWebsiteBundle\Contentful\ImageAssetScaler */
        $assetRepo   = $this->getContainer()->get('dothiv_contentful.repo.asset');
        $imageScaler = $this->getContainer()->get('dothiv.websitebase.contentful.image_scaler');
        $imageScaler->setLogger(new OutputInterfaceLogger($output));
        foreach ($assetRepo->findAll() as $asset) {
            $imageScaler->scaleAsset($asset);
        }
    }
} 
