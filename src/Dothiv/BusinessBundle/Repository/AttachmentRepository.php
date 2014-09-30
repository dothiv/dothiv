<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\Attachment;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use PhpOption\Option;

class AttachmentRepository extends DoctrineEntityRepository implements AttachmentRepositoryInterface
{
    use ValidatorTrait;
    
    /**
     * {@inheritdoc}
     */
    public function persist(Attachment $attachment)
    {
        $this->getEntityManager()->persist($this->validate($attachment));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
        return $this;
    }

    /**
     * @param string $handle
     *
     * @return Option
     */
    public function getAttachmentByHandle($handle)
    {
        return Option::fromValue($this->createQueryBuilder('a')
            ->andWhere('a.handle = :handle')->setParameter('handle', $handle)
            ->getQuery()
            ->getOneOrNullResult());
    }
}
