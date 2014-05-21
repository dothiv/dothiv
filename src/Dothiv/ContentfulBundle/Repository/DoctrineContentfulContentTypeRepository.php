<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use PhpOption\Option;

class DoctrineContentfulContentTypeRepository extends EntityRepository implements ContentfulContentTypeRepository
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
    function persist(ContentfulContentType $contentType)
    {
        $this->getEntityManager()->persist($contentType);
    }

}
