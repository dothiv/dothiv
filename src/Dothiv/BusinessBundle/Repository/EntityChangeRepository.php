<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dothiv\AdminBundle\AdminEvents;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\Traits;
use Dothiv\ValueObject\IdentValue;

class EntityChangeRepository extends EntityRepository implements EntityChangeRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\EventDispatcherTrait;
    use Traits\PaginatedQueryTrait;

    /**
     * @var EntityChange[]
     */
    private $entities = array();

    /**
     * @param EntityChange $change
     *
     * @return self
     */
    public function persist(EntityChange $change)
    {
        $this->getEntityManager()->persist($this->validate($change));
        $this->entities[] = $change;
        return $this;
    }

    /**
     * @return self
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
        foreach ($this->entities as $entity) {
            $this->eventDispatcher->dispatch(AdminEvents::ADMIN_ENTITY_CHANGE, new EntityChangeEvent($entity, $entity));
        }
        $this->entities = array();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated($entity, IdentValue $identifier, CRUD\PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->andWhere('i.entity = :entity')->setParameter('entity', $entity);
        $qb->andWhere('i.identifier = :identifier')->setParameter('identifier', $identifier->toScalar());
        return $this->buildPaginatedResult($qb, $options);
    }
}
