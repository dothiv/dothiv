<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Service\IRegistration;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command can be used to declare a domain to be registered.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 * @author Markus Tacker <m@dothiv.org>
 */
class RegistrationRegisteredCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:registration:registered')
            ->setDescription('Take action for a new registered domain.')
            ->addArgument('domain', InputArgument::REQUIRED, 'The name of the registered domain.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email address of the registrant.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the registrant.')
            ->addArgument('extId', InputArgument::REQUIRED, 'Registrar ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var IRegistration $reg */
        /* @var Domain $domain */
        $reg    = $this->getContainer()->get('registration');
        $domain = $reg->registered(
            $input->getArgument('domain'),
            $input->getArgument('email'),
            $input->getArgument('name'),
            $input->getArgument('extId')
        );
        $output->writeln('Registration token for ' . $domain->getName() . ': ' . $domain->getToken());

        // clear mail spool, see http://symfony.com/doc/2.0/cookbook/console/sending_emails.html
        $this->getContainer()->get('mailer')->getTransport()->getSpool()->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
    }
}
