<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\Traits;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use PhpOption\Option;

class UserProfileChangeRepository extends DoctrineEntityRepository implements UserProfileChangeRepositoryInterface
{
    use Traits\PaginatedQueryTrait;
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

    /**
     * {@inheritdoc}
     */
    public function persist(UserProfileChange $UserProfileChange)
    {
        $this->getEntityManager()->persist($this->validate($UserProfileChange));
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
    public function persistItem(EntityInterface $item)
    {
        $this->persist($item);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(CRUD\PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        $qb = $this->createQueryBuilder('i');
        $filterQuery->getUser()->map(function (User $user) use ($qb) {
            $qb->andWhere('i.user = :user')->setParameter('user', $user);
        });
        return $this->buildPaginatedResult($qb, $options);
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
     * Creates a new entity.
     *
     * @return EntityInterface
     */
    public function createItem()
    {
        return new UserProfileChange();
    }
}
