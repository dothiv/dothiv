<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendDomainNotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:notify:domain')
            ->setDescription('Notifify the owner of a domain')
            ->addArgument('domain', InputArgument::REQUIRED, 'Name of the domain')
            ->addArgument('template', InputArgument::REQUIRED, 'SendWithUs Template ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var DomainRepositoryInterface $domainRepo */
        $domainRepo = $this->getContainer()->get('dothiv.repository.domain');
        /** @var UserReminderMailer $mailer */
        $mailer = $this->getContainer()->get('dothiv.business.userreminder.mailer');

        $name = HivDomainValue::createFromUTF8($input->getArgument('domain'));
        /** @var Domain $domain */
        $domain = $domainRepo->getDomainByName($name->toScalar())->get();
        $data   = [
            'firstname' => $domain->getOwner()->getFirstname(),
            'lastname'  => $domain->getOwner()->getSurname(),
            'domain'    => $name->toUTF8()
        ];
        $mailer->send(
            $data,
            new EmailValue($domain->getOwner()->getEmail()),
            $domain->getOwner()->getFirstname() . ' ' . $domain->getOwner()->getSurname(),
            $input->getArgument('template')
        );
    }
}
