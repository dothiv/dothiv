<?php

namespace Dothiv\BusinessBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to sync the local database with the click counter
 * cloud application.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class ClickCounterRetrieveCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('dothiv:clickcounter:update')
            ->setDescription('Update domain data from the click counter API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $cc = $this->getContainer()->get('clickcounter');
        // FIXME: use clock service
        list($count, $fail) = $cc->retrieveByDate(new \DateTime());
        if ($fail > 0)
            $output->writeln('Update failed on ' . $fail . ' of ' . $count . ' domains!');
        else
            $output->writeln('Updated values on ' . $count . ' domains');
    }

}
