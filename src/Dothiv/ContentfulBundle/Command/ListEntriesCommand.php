<?php

namespace Dothiv\ContentfulBundle\Command;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Dothiv\ContentfulBundle\Adapter\HttpClientAdapter;
use Dothiv\ContentfulBundle\Logger\OutputInterfaceLogger;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepositoryInterface;
use Dothiv\ContentfulBundle\Repository\ContentfulContentTypeRepository;
use Dothiv\ContentfulBundle\Repository\ContentfulEntryRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListEntriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:list:entries')
            ->setDescription('List local contentful entries');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContentfulEntryRepository $repo */
        /** @var TableHelper $table */
        $repo        = $this->getContainer()->get('dothiv_contentful.repo.entry');
        $entries     = array();
        $entriesSort = array();
        foreach ($repo->findAll() as $entry) {
            $entries[]     = array(
                $entry->getSpaceId(),
                $entry->getId(),
                $entry->getName(),
                $entry->getRevision(),
            );
            $entriesSort[] = $entry->getSpaceId();
        }
        array_multisort($entriesSort, SORT_ASC, $entries);
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array('Space', 'Id', 'Name', 'Revision'));
        $table->setRows($entries);
        $table->render($output);
    }
}
