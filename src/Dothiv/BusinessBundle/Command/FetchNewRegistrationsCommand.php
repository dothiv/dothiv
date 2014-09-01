<?php

namespace Dothiv\BusinessBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to fetch new domain registrations and register the in the app
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class FetchNewRegistrationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:registrations:fetch')
            ->setDescription('Fetch new domain registrations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
