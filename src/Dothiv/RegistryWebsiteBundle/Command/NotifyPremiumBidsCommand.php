<?php

namespace Dothiv\RegistryWebsiteBundle\Command;

use Dothiv\BusinessBundle\Repository\PremiumBidRepositoryInterface;
use Dothiv\BusinessBundle\Service\Clock;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates an item for every bid in a podio app.
 */
class NotifyPremiumBidsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('registry:premiumbid:notify')
            ->setDescription('Send notifications about premium bids')
            ->addOption('appId', 'i', InputOption::VALUE_REQUIRED, 'Podio app id')
            ->addOption('appToken', 't', InputOption::VALUE_REQUIRED, 'App token')
            ->addOption('clientId', 'c', InputOption::VALUE_REQUIRED, 'Client id')
            ->addOption('clientSecret', 'S', InputOption::VALUE_REQUIRED, 'Client secret')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $premiumBidRepo PremiumBidRepositoryInterface */
        /** @var $clock Clock */
        $premiumBidRepo = $this->getContainer()->get('dothiv.repository.premiumbid');
        $clock          = $this->getContainer()->get('clock');

        $appId = $input->getOption('appId');
        \Podio::setup($input->getOption('clientId'), $input->getOption('clientSecret'));
        \Podio::authenticate_with_app($appId, $input->getOption('appToken'));

        foreach ($premiumBidRepo->getUnnotified() as $bid) {
            $output->writeln($bid->getDomain());
            \PodioItem::create($appId, array(
                'fields' => array(
                    'hiv-domain' => $bid->getDomain(),
                    'firstname'  => $bid->getFirstname(),
                    'surname'    => $bid->getSurname(),
                )
            ));
            $bid->setNotified($clock->getNow());
            $premiumBidRepo->persist($bid)->flush();
        }
    }
} 
