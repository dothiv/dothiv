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

class ListContentTypesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:list:types')
            ->setDescription('List local contentful content types');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContentfulContentTypeRepository $repo */
        /** @var TableHelper $table */
        $repo             = $this->getContainer()->get('dothiv_contentful.repo.content_type');
        $contentTypes     = array();
        $contentTypesSort = array();
        foreach ($repo->findAll() as $contentType) {
            $contentTypes[]     = array(
                $contentType->getSpaceId(),
                $contentType->getId(),
                $contentType->getName(),
                $contentType->getRevision(),
            );
            $contentTypesSort[] = $contentType->getSpaceId();
        }
        array_multisort($contentTypesSort, SORT_ASC, $contentTypes);
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array('Space', 'Id', 'Name', 'Revision'));
        $table->setRows($contentTypes);
        $table->render($output);
    }
}
