<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use PhpOption\Option;

class DoctrineContentfulAssetRepository extends EntityRepository implements ContentfulAssetRepository
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
    function persist(ContentfulAsset $asset)
    {
        $this->getEntityManager()->persist($asset);
    }

}
