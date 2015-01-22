<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\RegistrarRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Repository\OrderRepositoryInterface;
use Dothiv\ShopBundle\Service\InvoiceServiceInterface;
use Dothiv\ShopBundle\Service\OrderMailerInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SendDomainNotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:notify:domain')
            ->setDescription('Notifify the owner of a domain')
            ->addArgument('domain', InputArgument::REQUIRED, 'Name of the domain')
            ->addArgument('template', InputArgument::REQUIRED, 'SendWithUs Template ID')
            ->addArgument('version', InputArgument::REQUIRED, 'Template version');
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
            $input->getArgument('template'),
            $input->getArgument('version')
        );
    }
}
