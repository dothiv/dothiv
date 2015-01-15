<?php

namespace Dothiv\UserReminderBundle\Command;

use Dothiv\UserReminderBundle\Events\UserReminderEvent;
use Dothiv\UserReminderBundle\Service\UserReminderRegistryInterface;
use Dothiv\UserReminderBundle\UserReminderEvents;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sends pending user reminders
 */
class SendRemindersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:reminders:send')
            ->setDescription('Sends pending user reminders');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UserReminderRegistryInterface $service */
        $service = $this->getContainer()->get('dothiv.userreminder.registry');
        $this->logEventsTo($output);
        $service->send();
        $this->flushMailQueue();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function logEventsTo(OutputInterface $output)
    {
        if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            /** @var EventDispatcherInterface $dispatcher */
            $dispatcher = $this->getContainer()->get('dothiv.business.event_dispatcher');
            $dispatcher->addListener(UserReminderEvents::REMINDER_SENT, function (UserReminderEvent $event) use ($output) {
                $output->writeln(sprintf('%s > %s', $event->getReminder()->getType(), $event->getReminder()->getIdent()));
            });
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function flushMailQueue()
    {
        // clear mail spool, see http://symfony.com/doc/2.0/cookbook/console/sending_emails.html
        if ($this->getContainer()->getParameter("kernel.environment") != 'test') {
            $this->getContainer()->get('mailer')->getTransport()->getSpool()->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
        }
    }
}
