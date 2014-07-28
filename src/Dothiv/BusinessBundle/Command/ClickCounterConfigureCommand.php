<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Service\ClickCounterConfigInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to update the click counter configuration.
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class ClickCounterConfigureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:clickcounter:configure')
            ->setDescription('Update the configuration of a clickcounter.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $cc ClickCounterConfigInterface */
        /* @var $bannerRepo BannerRepositoryInterface */
        /* @var $banners Banner[] */
        $cc         = $this->getContainer()->get('clickcounter');
        $bannerRepo = $this->getContainer()->get('dothiv.repository.banner');
        $banners    = $bannerRepo->findAll();
        if (empty($banners)) {
            $output->writeln('No banners found!');
        }
        foreach ($banners as $banner) {
            $output->writeln(sprintf('Updateing %s ...', $banner->getDomain()->getName()));
            $cc->setup($banner);
        }
    }
}
