<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\BusinessBundle\Service\ClickCounterConfigInterface;
use Dothiv\ValueObject\ClockValue;
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
        $config = $this->getConfig();
        /* @var $cc ClickCounterConfigInterface */
        /* @var $banners Banner[] */
        $cc = $this->getContainer()->get('clickcounter');
        if (Option::fromValue($config->getValue())->isEmpty() || $input->getOption('force')) {
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                $output->writeln('Fetching all click-counters ...');
            }
            $banners = $this->findAll();
        } else {
            $banners = $this->findUpdatedSince(new \DateTime($config->getValue()));
        }
        foreach ($banners as $banner) {
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                $output->writeln(sprintf('Updating %s ...', $banner->getDomain()->getName()));
            }
            $cc->setup($banner);
        }
        /** @var ClockValue $clock */
        $clock = $this->getContainer()->get('clock');
        $config->setValue($clock->getNow()->format(DATE_W3C));

        $this->getConfigRepo()->persist($config)->flush();
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->getConfigRepo()->get('clickcounter_config.last_run');
    }

    /**
     * @return Banner[]
     */
    protected function findAll()
    {
        $bannerRepo = $this->getContainer()->get('dothiv.repository.banner');
        return $bannerRepo->findAll();
    }

    /**
     * @param \DateTime $time
     *
     * @return Banner[]
     */
    protected function findUpdatedSince(\DateTime $time)
    {
        $bannerRepo = $this->getContainer()->get('dothiv.repository.banner');
        return $bannerRepo->findUpdatedSince($time);
    }

    /**
     * @return ConfigRepositoryInterface
     */
    protected function getConfigRepo()
    {
        return $this->getContainer()->get('dothiv.repository.config');
    }
}
