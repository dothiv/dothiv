<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\DeleteEntityRepositoryInterface;
use Dothiv\BusinessBundle\Repository\Traits;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use PhpOption\Option;

class DomainCollaboratorRepository extends DoctrineEntityRepository implements DomainCollaboratorRepositoryInterface
{
    use Traits\PaginatedQueryTrait;
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

    /**
     * {@inheritdoc}
     */
    public function createItem()
    {
        return new DomainCollaborator();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(CRUD\PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        $qb = $this->createQueryBuilder('i');
        $filterQuery->getUser()->map(function (User $user) use ($qb) {
            $qb->leftJoin('i.domain', 'd');
            $qb->andWhere('d.owner = :user')->setParameter('user', $user);
        });
        return $this->buildPaginatedResult($qb, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem(EntityInterface $item)
    {
        $this->getEntityManager()->remove($item);
        return $this;
    }

}
