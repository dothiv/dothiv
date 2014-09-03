<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\BusinessBundle\Service\ClickCounterConfigInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to update the click counter configuration.
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class ClickCounterStatsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:clickcounter:stats')
            ->setDescription('Fetch the clickcounter statistics.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $cc ClickCounterConfigInterface */
        /** @var $bannerRepo ConfigRepositoryInterface */
        /** @var Config $config */
        $cc         = $this->getContainer()->get('clickcounter');
        $configRepo = $this->getContainer()->get('dothiv.repository.config');
        $clickcount = $cc->getClickCount();
        $config     = $configRepo->get('clickcount');
        if ($clickcount != (int)$config->getValue()) {
            $config->setValue($clickcount);
            $configRepo->persist($config)->flush();
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln('New clickcount: ' . $clickcount);
            }
        }
    }
}
