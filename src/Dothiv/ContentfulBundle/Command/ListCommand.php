<?php

namespace Dothiv\ContentfulBundle\Command;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Dothiv\ContentfulBundle\Adapter\HttpClientAdapter;
use Dothiv\ContentfulBundle\Logger\OutputInterfaceLogger;
use Dothiv\ContentfulBundle\Repository\ContentfulContentTypeRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:list')
            ->setDescription('List local contentful items')
            ->addOption('what', 'what', InputOption::VALUE_REQUIRED, 'What to list.', 'contenttype');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        switch ($input->getOption('what')) {
            default:
                $this->listContentTypes($output);
        }
    }

    protected function listContentTypes(OutputInterface $output)
    {
        /** @var ContentfulContentTypeRepository $repo */
        $repo = $this->getContainer()->get('dothiv_contentful.repo.content_type');
        foreach ($repo->findAll() as $contenttype) {
            $output->writeln((string)$contenttype);
        }
    }
} 
