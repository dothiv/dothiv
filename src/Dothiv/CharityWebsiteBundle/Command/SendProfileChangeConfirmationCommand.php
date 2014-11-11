<?php

namespace Dothiv\CharityWebsiteBundle\Command;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Repository\UserProfileChangeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendProfileChangeConfirmationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('charity:profile:send_confirm_change_email')
            ->setDescription('Send emails asking the users to confirm their profile changes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UserProfileChangeRepositoryInterface $userProfileChangeRepo */
        $userProfileChangeRepo = $this->getContainer()->get('dothiv.repository.user_profile_change');
        /** @var ContentMailerInterface $mailer */
        $mailer = $this->getContainer()->get('dothiv.charity.service.mailer.content');

        foreach ($userProfileChangeRepo->findUnsent() as $userProfileChange) {
            $mailer->sendContentTemplateMail(
                'profile.change.confirm',
                'en',
                $userProfileChange->getUser()->getEmail(),
                $userProfileChange->getUser()->getFirstname() . ' ' . $userProfileChange->getUser()->getSurname(),
                array('change' => $userProfileChange)
            );
            $userProfileChange->setSent(true);
            $userProfileChangeRepo->persist($userProfileChange);
        }
        $userProfileChangeRepo->flush();
    }
}
