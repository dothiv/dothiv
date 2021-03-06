<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\Traits;
use PhpOption\Option;

class RegistrarRepository extends DoctrineEntityRepository implements RegistrarRepositoryInterface
{
    use Traits\PaginatedQueryTrait;
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

    /**
     * {@inheritdoc}
     */
    public function persist(Registrar $premiumBid)
    {
        $this->getEntityManager()->persist($this->validate($premiumBid));
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
     * @param string $extId
     *
     * @return Option
     */
    public function findByExtId($extId)
    {
        return Option::fromValue(
            $this->createQueryBuilder('r')
                ->andWhere('r.extId = :extId')->setParameter('extId', $extId)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * Returns a registrar for the given $extId, if the registrar does not exist, it is created.
     *
     * @param string $extId
     *
     * @return Registrar
     */
    public function getByExtId($extId)
    {
        return $this->findByExtId($extId)->getOrCall(function () use ($extId) {
            $registrar = new Registrar();
            $registrar->setExtId($extId);
            $this->persist($registrar)->flush();
            return $registrar;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return Option::fromValue($this->getByExtId($identifier));
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(CRUD\PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        return $this->buildPaginatedResult($this->createQueryBuilder('i'), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function persistItem(EntityInterface $item)
    {
        $this->persist($item);
        return $this;
    }
}
