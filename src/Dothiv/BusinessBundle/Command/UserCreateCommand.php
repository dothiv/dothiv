<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Dothiv\BusinessBundle\Service\WhoisReportParser;
use Dothiv\BusinessBundle\Service\WhoisServiceInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates a new user
 */
class UserCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:user:create')
            ->setDescription('Creates a new user')
            ->addArgument('email', InputArgument::REQUIRED, 'Email address')
            ->addArgument('firstname', InputArgument::REQUIRED, 'First name')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Last name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getUserService()->getOrCreateUser(
            EmailValue::create($input->getArgument('email'))->toScalar(),
            $input->getArgument('firstname'),
            $input->getArgument('lastname')
        );
    }

    /**
     * @return UserServiceInterface
     */
    protected function getUserService()
    {
        return $this->getContainer()->get('dothiv.businessbundle.service.user');
    }

}
