<?php

namespace Dothiv\ContentfulBundle\Command;

use Dothiv\ContentfulBundle\Client\HttpClient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WebhookListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:webhooks:list')
            ->setDescription('List configured webhooks for the given space')
            ->addOption('space', 'S', InputOption::VALUE_REQUIRED, 'ID of the space', 'cfexampleapi')
            ->addOption('access_token', 't', InputOption::VALUE_REQUIRED, 'Access token', 'b4c0n73n7fu1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client   = new HttpClient($input->getOption('access_token'), 'application/vnd.contentful.management.v1+json');
        $response = $client->get(sprintf('https://api.contentful.com/spaces/%s/webhook_definitions', $input->getOption('space')));
        $data     = json_decode($response);
        if (!$data->items) {
            $output->writeln('No hooks defined.');
        }
        foreach ($data->items as $hook) {
            $output->writeln(sprintf('%s: %s', $hook->sys->id, $hook->url));
        }
    }
} 
