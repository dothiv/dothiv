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
    public function findNewestById($id)
    {
        $result = $this->findBy(array('id' => $id), array('revision' => 'DESC'), 1);
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
    public function findNewestByName($name)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT c1 FROM Dothiv\ContentfulBundle\Item\ContentfulContentType c1 '
            . 'WHERE c1.name = :name '
            . 'AND c1.revision = (SELECT MAX(c2.revision) FROM Dothiv\ContentfulBundle\Item\ContentfulContentType c2 WHERE c2.id = c1.id)'
        )->setParameter('name', $name);
        return new ArrayCollection($query->getResult());
    }
}
