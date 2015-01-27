<?php


namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Repository\CRUD\EntityRepositoryInterface;
use Dothiv\BusinessBundle\Repository\CRUD\ReadEntityRepositoryInterface;
use Dothiv\BusinessBundle\Repository\Traits;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\Option;

class DomainWhoisRepository extends DoctrineEntityRepository implements DomainWhoisRepositoryInterface, EntityRepositoryInterface, ReadEntityRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

    /**
     * {@inheritdoc}
     */
    public function persist(DomainWhois $domainWhois)
    {
        $this->getEntityManager()->persist($this->validate($domainWhois));
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
    public function findByDomain(HivDomainValue $domain)
    {
        return Option::fromValue(
            $this->createQueryBuilder('d')
                ->andWhere('d.domain = :domain')->setParameter('domain', $domain->toScalar())
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return $this->findByDomain(new HivDomainValue($identifier));
    }

}
