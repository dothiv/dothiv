<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Service\Registration;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command can be used to declare a domain to be deleted.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class RegistrationDeletedCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
        ->setName('dothiv:registration:deleted')
        ->setDescription('Take action for a deleted domain.')
        ->addArgument('name', InputArgument::REQUIRED, 'The name of the deleted domain.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $reg = $this->getContainer()->get('registration');
        $reg->deleted($input->getArgument('name'));
        $output->writeln('Deleted domain ' . $input->getArgument('name') . '.');

        // clear mail spool, see http://symfony.com/doc/2.0/cookbook/console/sending_emails.html
        $this->getContainer()->get('mailer')->getTransport()->getSpool()->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
    }

}
