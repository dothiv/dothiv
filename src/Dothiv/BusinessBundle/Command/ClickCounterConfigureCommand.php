<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\BusinessBundle\Service\ClickCounterConfigInterface;
use Dothiv\BusinessBundle\Service\Clock;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Update the configuration of a clickcounter.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $configRepo ConfigRepositoryInterface */
        /** @var Config $config */
        $configRepo = $this->getContainer()->get('dothiv.repository.config');
        $config     = $configRepo->get('clickcounter_config.last_run');
        /* @var $cc ClickCounterConfigInterface */
        /* @var $bannerRepo BannerRepositoryInterface */
        /* @var $banners Banner[] */
        $cc         = $this->getContainer()->get('clickcounter');
        $bannerRepo = $this->getContainer()->get('dothiv.repository.banner');
        if (Option::fromValue($config->getValue())->isEmpty() || $input->getOption('force')) {
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                $output->writeln('Fetching all click-counters ...');
            }
            $banners = $bannerRepo->findAll();
        } else {
            $banners = $bannerRepo->findUpdatedSince(new \DateTime($config->getValue()));
        }
        foreach ($banners as $banner) {
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                $output->writeln(sprintf('Updating %s ...', $banner->getDomain()->getName()));
            }
            $cc->setup($banner);
        }
        /** @var Clock $clock */
        $clock = $this->getContainer()->get('clock');
        $config->setValue($clock->getNow()->format(DATE_W3C));
        $configRepo->persist($config)->flush();
    }
}
