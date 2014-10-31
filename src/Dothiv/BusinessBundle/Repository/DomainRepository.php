<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\Traits;
use Dothiv\BusinessBundle\Service\FilterQueryParser;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;

class DomainRepository extends DoctrineEntityRepository implements DomainRepositoryInterface
{
    use Traits\PaginatedQueryTrait;
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

    /**
     * @param string $name
     *
     * @return Option
     */
    public function getDomainByName($name)
    {
        return Option::fromValue(
            $this->createQueryBuilder('d')
                ->andWhere('d.name = :name')->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * @param string $token
     *
     * @return Option
     */
    public function getDomainByToken($token)
    {
        return Option::fromValue(
            $this->createQueryBuilder('d')
                ->andWhere('d.token = :token')->setParameter('token', $token)
                ->andWhere('d.token IS NOT NULL')
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Domain $domain)
    {
        $this->getEntityManager()->persist($this->validate($domain));
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
    public function remove(Domain $domain)
    {
        $this->getEntityManager()->remove($domain);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        $qb = $this->createQueryBuilder('i');
        if ($filterQuery->getTerm()->isDefined()) {
            $qb->andWhere('i.name LIKE :q')->setParameter('q', '%' . $filterQuery->getTerm()->get() . '%');
        }
        if ($filterQuery->getProperty('transfer')->isDefined()) {
            $qb->andWhere('i.transfer = :transfer')->setParameter('transfer', (int)$filterQuery->getProperty('transfer')->get());
        }
        if ($filterQuery->getProperty('nonprofit')->isDefined()) {
            $qb->andWhere('i.nonprofit = :nonprofit')->setParameter('nonprofit', (int)$filterQuery->getProperty('nonprofit')->get());
        }
        if ($filterQuery->getProperty('registrar')->isDefined()) {
            // TODO: Implement URL to public-id for entities.
            $url       = new URLValue($filterQuery->getProperty('registrar')->get());
            $pathParts = explode('/', $url->getPath());
            $qb->leftJoin('i.registrar', 'r');
            $qb->andWhere('r.extId = :extId')->setParameter('extId', array_pop($pathParts));
        }
        return $this->buildPaginatedResult($qb, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return $this->getDomainByName($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function findUninstalled()
    {
        return new ArrayCollection(
            $this->createQueryBuilder('d')
                ->andWhere('d.activeBanner IS NOT NULL')
                ->andWhere('d.clickcount = 0')
                ->getQuery()
                ->getResult()
        );
    }
}
