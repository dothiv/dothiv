<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Repository\Traits;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use PhpOption\Option;

class DomainCollaboratorRepository extends DoctrineEntityRepository implements DomainCollaboratorRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

    /**
     * {@inheritdoc}
     */
    public function persist(DomainCollaborator $DomainCollaborator)
    {
        $this->getEntityManager()->persist($this->validate($DomainCollaborator));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persistItem(EntityInterface $item)
    {
        $this->persist($item);
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
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return Option::fromValue(
            $this->createQueryBuilder('c')
                ->andWhere('c.id = :id')->setParameter('id', $identifier)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }
}
