<?php


namespace Dothiv\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\DeletedDomain;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\EntityRepositoryInterface;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedReadEntityRepositoryInterface;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedResult;
use Dothiv\BusinessBundle\Repository\CRUD\ReadEntityRepositoryInterface;
use Dothiv\BusinessBundle\Repository\Traits;
use PhpOption\Option;

class DeletedDomainRepository extends DoctrineEntityRepository implements DeletedDomainRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;
    use Traits\PaginatedQueryTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(DeletedDomain $DeletedDomain)
    {
        $this->getEntityManager()->persist($this->validate($DeletedDomain));
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
        return Option::fromValue($this->findBy($identifier));
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        $qb = $this->createQueryBuilder('i');
        if ($filterQuery->getTerm()->isDefined()) {
            $qb->andWhere('i.domain LIKE :q')->setParameter('q', '%' . $filterQuery->getTerm()->get() . '%');
        }
        return $this->buildPaginatedResult($qb, $options);
    }
}
