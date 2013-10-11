<?php

namespace DotHiv\BusinessBundle\Command;

use DotHiv\BusinessBundle\Service\Registration;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command can be used to declare a domain to be registered.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class RegistrationRegisteredCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('dothiv:registration:registered')
            ->setDescription('Take action for a new registered domain.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the registered domain.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email address of the registrant.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $reg = $this->getContainer()->get('registration');
        $domain = $reg->registered($input->getArgument('name'), $input->getArgument('email'));
        $output->writeln('Registration token for ' . $domain->getName() . ': ' . $domain->getClaimingToken());

        // clear mail spool, see http://symfony.com/doc/2.0/cookbook/console/sending_emails.html
        $this->getContainer()->get('mailer')->getTransport()->getSpool()->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
    }

}
