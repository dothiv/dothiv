<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use PhpOption\Option;

class DoctrineContentfulAssetRepository extends EntityRepository implements ContentfulAssetRepository
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
    function persist(ContentfulAsset $asset)
    {
        $this->getEntityManager()->persist($asset);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT a1 FROM Dothiv\ContentfulBundle\Item\ContentfulAsset a1 '
            . 'WHERE a1.revision = (SELECT MAX(a2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulAsset a2 WHERE a2.id = a1.id) '
        );
        return new ArrayCollection($query->getResult());
    }

    /**
     * {@inheritdoc}
     */
    public function findAllBySpaceId($spaceId)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT a1 FROM Dothiv\ContentfulBundle\Item\ContentfulAsset a1 '
            . 'WHERE a1.revision = (SELECT MAX(a2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulAsset a2 WHERE a2.id = a1.id AND a2.spaceId = :spaceId) '
            . 'AND a1.spaceId = :spaceId'
        )->setParameter('spaceId', $spaceId);
        return new ArrayCollection($query->getResult());
    }
}
