<?php

namespace Dothiv\ContentfulBundle\Command;

use Dothiv\ContentfulBundle\Client\HttpClient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;

class WebhookRegisterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:webhooks:register')
            ->setDescription('List configured webhooks for the given space')
            ->addOption('space', 'S', InputOption::VALUE_REQUIRED, 'ID of the space', 'cfexampleapi')
            ->addOption('host', 'H', InputOption::VALUE_REQUIRED, 'HTTP host', 'https://example.com')
            ->addOption('id', 'I', InputOption::VALUE_OPTIONAL, 'ID of the hook', 'symfony2')
            ->addOption('access_token', 't', InputOption::VALUE_REQUIRED, 'Access token', 'b4c0n73n7fu1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var RouterInterface $router */
        $router   = $this->getContainer()->get('router');
        $client   = new HttpClient($input->getOption('access_token'), 'application/vnd.contentful.management.v1+json');
        $config   = $this->getContainer()->getParameter('dothiv_contentful.webhook');
        $response = $client->put(
            sprintf('https://api.contentful.com/spaces/%s/webhook_definitions/%s', $input->getOption('space'), $input->getOption('id')),
            array(
                'url'               => sprintf(
                    '%s%s',
                    $input->getOption('host'),
                    $router->generate('dothiv_contentful_webhook')
                ),
                'httpBasicUsername' => $config['httpBasicUsername'],
                'httpBasicPassword' => $config['httpBasicPassword']
            )
        );
        $data     = json_decode($response);
        $output->writeln(sprintf('Created hook %s', $data->sys->id));
    }
} 
