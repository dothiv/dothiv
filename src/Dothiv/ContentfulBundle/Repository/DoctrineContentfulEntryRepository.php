<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use PhpOption\Option;

class DoctrineContentfulEntryRepository extends EntityRepository implements ContentfulEntryRepository
{
    /**
     * {@inheritdoc}
     */
    function findNewestById($spaceId, $id)
    {
        $result = $this->findBy(array('id' => $id, 'spaceId' => $spaceId), array('revision' => 'DESC'), 1);
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
     * {@inheritdoc}
     */
    function remove(ContentfulEntry $entry)
    {
        $this->getEntityManager()->remove($entry);
    }

    /**
     * @param ContentfulContentType $contentType
     *
     * @return ContentfulEntry[]|ArrayCollection
     */
    function findByContentType(ContentfulContentType $contentType)
    {
        // Do not rely on Mysql Group By.
        $query   = $this->getEntityManager()->createQuery(
            'SELECT e1 FROM Dothiv\ContentfulBundle\Item\ContentfulEntry e1 '
            . 'WHERE e1.contentTypeId = :contentTypeId '
            . 'AND e1.spaceId = :spaceId '
            . 'AND e1.revision = (SELECT MAX(e2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulEntry e2 WHERE e2.id = e1.id AND e2.spaceId = :spaceId)')
            ->setParameter('contentTypeId', $contentType->getId())
            ->setParameter('spaceId', $contentType->getSpaceId())
        ;
        return new ArrayCollection($query->getResult());
    }

    /**
     * @param string $spaceId
     * @param string $contentTypeId
     * @param string $name
     *
     * @return Option
     */
    function findByContentTypeIdAndName($spaceId, $contentTypeId, $name)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT e1 FROM Dothiv\ContentfulBundle\Item\ContentfulEntry e1 '
            . 'WHERE e1.name = :name '
            . 'AND e1.contentTypeId = :contentTypeId '
            . 'AND e1.spaceId = :spaceId '
            . 'AND e1.revision = (SELECT MAX(e2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulEntry e2 WHERE e2.id = e1.id AND e2.spaceId = :spaceId)'
        )
            ->setParameter('contentTypeId', $contentTypeId)
            ->setParameter('spaceId', $spaceId)
            ->setParameter('name', $name)
        ;
        return Option::fromValue($query->getOneOrNullResult());
    }
}
