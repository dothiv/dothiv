<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
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
 * Sets the owner of a domain to the given user
 */
class DomainOwnerSetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:domain:owner')
            ->setDescription('Sets the owner of a domain')
            ->addArgument('domain', InputArgument::REQUIRED, 'Domain name')
            ->addArgument('email', InputArgument::REQUIRED, 'Email address of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userOptional = $this->getUserRepo()->getUserByEmail($input->getArgument('email'));
        if ($userOptional->isEmpty()) {
            throw new InvalidArgumentException(
                sprintf('User "%s" not found.', $input->getArgument('email'))
            );
        }
        $domainOptional = $this->getDomainRepo()->getDomainByName($input->getArgument('domain'));
        if ($domainOptional->isEmpty()) {
            throw new InvalidArgumentException(
                sprintf('Domain "%s" not found.', $input->getArgument('domain'))
            );
        }
        /** @var Domain $domain */
        $domain = $domainOptional->get();
        $domain->setOwner($userOptional->get());
        $this->getDomainRepo()->persist($domain)->flush();
    }

    /**
     * @return UserRepositoryInterface
     */
    protected function getUserRepo()
    {
        return $this->getContainer()->get('dothiv.repository.user');
    }

    /**
     * @return DomainRepositoryInterface
     */
    protected function getDomainRepo()
    {
        return $this->getContainer()->get('dothiv.repository.domain');
    }

}
