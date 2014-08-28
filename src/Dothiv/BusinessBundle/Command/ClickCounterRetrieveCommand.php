<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Service\ClickCounterConfigInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to sync the local database with the click counter
 * cloud application.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class ClickCounterRetrieveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:clickcounter:update')
            ->setDescription('Update domain data from the click counter API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $cc ClickCounterConfigInterface */
        /* @var $bannerRepo BannerRepositoryInterface */
        /* @var $domainRepo DomainRepositoryInterface */
        /* @var $banners Banner[] */
        $cc         = $this->getContainer()->get('clickcounter');
        $bannerRepo = $this->getContainer()->get('dothiv.repository.banner');
        $domainRepo = $this->getContainer()->get('dothiv.repository.domain');
        $banners    = $bannerRepo->findAll();
        if (empty($banners)) {
            $output->writeln('No banners found!');
        }
        foreach ($banners as $banner) {
            $config = $cc->get($banner->getDomain());
            $domain = $banner->getDomain();
            $domain->setClickcount($config->clicks_domain);
            $domainRepo->persist($domain)->flush();
            $output->writeln(sprintf('%s:  %d', $domain->getName(), $domain->getClickcount()));
        }
    }
}
