<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use PhpOption\Option;

class DoctrineContentfulContentTypeRepository extends EntityRepository implements ContentfulContentTypeRepository
{
    /**
     * {@inheritdoc}
     */
    public function findNewestById($spaceId, $id)
    {
        $result = $this->findBy(array('id' => $id, 'spaceId' => $spaceId), array('revision' => 'DESC'), 1);
        return Option::fromValue(count($result) == 1 ? array_shift($result) : null);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(ContentfulContentType $contentType)
    {
        $this->getEntityManager()->persist($contentType);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ContentfulContentType $contentType)
    {
        $this->getEntityManager()->remove($contentType);
    }

    /**
     * {@inheritdoc}
     */
    public function findNewestByName($spaceId, $name)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT c1 FROM Dothiv\ContentfulBundle\Item\ContentfulContentType c1 '
            . 'WHERE c1.name = :name '
            . 'AND c1.spaceId = :spaceId '
            . 'AND c1.revision = (SELECT MAX(c2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulContentType c2 WHERE c2.id = c1.id AND c2.spaceId = :spaceId)'
        )
            ->setParameter('name', $name)
            ->setParameter('spaceId', $spaceId);
        return new ArrayCollection($query->getResult());
    }

    /**
     * {@inheritdoc}
     */
    public function findAllBySpaceId($spaceId)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT c1 FROM Dothiv\ContentfulBundle\Item\ContentfulContentType c1 '
            . 'WHERE c1.revision = (SELECT MAX(c2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulContentType c2 WHERE c2.id = c1.id AND c2.spaceId = :spaceId) '
            . 'AND c1.spaceId = :spaceId'
        )->setParameter('spaceId', $spaceId);
        return new ArrayCollection($query->getResult());
    }
}
