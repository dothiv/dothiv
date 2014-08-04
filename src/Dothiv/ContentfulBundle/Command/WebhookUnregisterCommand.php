<?php

namespace Dothiv\ContentfulBundle\Command;

use Dothiv\ContentfulBundle\Client\HttpClient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;

class WebhookUnregisterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:webhooks:unregister')
            ->setDescription('Unregister webhook for the given space')
            ->addOption('space', 'S', InputOption::VALUE_REQUIRED, 'ID of the space', 'cfexampleapi')
            ->addOption('id', 'I', InputOption::VALUE_OPTIONAL, 'ID of the hook', 'symfony2')
            ->addOption('access_token', 't', InputOption::VALUE_REQUIRED, 'Access token', 'b4c0n73n7fu1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var RouterInterface $router */
        $client = new HttpClient($input->getOption('access_token'), 'application/vnd.contentful.management.v1+json');
        $client->delete(
            sprintf('https://api.contentful.com/spaces/%s/webhook_definitions/%s', $input->getOption('space'), $input->getOption('id'))
        );
    }
} 
