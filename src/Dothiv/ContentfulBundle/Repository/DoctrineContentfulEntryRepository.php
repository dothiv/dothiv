<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use PhpOption\Option;

class DoctrineContentfulEntryRepository extends EntityRepository implements ContentfulEntryRepository
{
    /**
     * {@inheritdoc}
     */
    function findNewestById($id)
    {
        $result = $this->findBy(array('id' => $id), array('revision' => 'DESC'), 1);
        return Option::fromValue(count($result) == 1 ? array_shift($result) : null);
    }

    /**
     * {@inheritdoc}
     */
    function persist(ContentfulEntry $entry)
    {
        $this->getEntityManager()->persist($entry);
    }

    /**
     * @param ContentfulContentType $contentType
     *
     * @return ContentfulEntry[]
     */
    function findByContentType(ContentfulContentType $contentType)
    {
        // Do not rely on Mysql Group By.
        $query   = $this->getEntityManager()->createQuery('SELECT e1 FROM Dothiv\ContentfulBundle\Item\ContentfulEntry e1 WHERE e1.contentTypeId = :contentTypeId AND e1.revision = (SELECT MAX(e2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulEntry e2 WHERE e2.id = e1.id)')->setParameter('contentTypeId', $contentType->getId());
        $entries = $query->getResult();
        array_map(function (ContentfulEntry $entry) use ($contentType) {
            $entry->setContentType($contentType);
        }, $entries);
        return $entries;
    }

    /**
     * @param string $contentTypeId
     * @param string $name
     *
     * @return Option
     */
    function findByContentTypeIdAndName($contentTypeId, $name)
    {
        $query = $this->getEntityManager()->createQuery('SELECT e1 FROM Dothiv\ContentfulBundle\Item\ContentfulEntry e1 WHERE e1.contentTypeId = :contentTypeId AND e1.revision = (SELECT MAX(e2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulEntry e2 WHERE e2.id = e1.id) AND e1.name = :name')
            ->setParameter('contentTypeId', $contentTypeId)
            ->setParameter('name', $name);
        return Option::fromValue($query->getOneOrNullResult());
    }
}
