<?php

namespace Dothiv\ShopBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dothiv\ShopBundle\Entity\DomainInfo;
use Dothiv\BusinessBundle\Repository\Traits;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\Option;

class DomainInfoRepository extends EntityRepository implements DomainInfoRepositoryInterface
{
    use Traits\ValidatorTrait;

    /**
     * @param DomainInfo $domainInfo
     *
     * @return self
     */
    public function persist(DomainInfo $domainInfo)
    {
        $this->getEntityManager()->persist($this->validate($domainInfo));
        return $this;
    }

    /**
     * @return self
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
        return $this;
    }

    /**
     * @param HivDomainValue $name
     *
     * @return DomainInfo
     */
    public function getByDomain(HivDomainValue $name)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->andWhere('i.name = :name')->setParameter('name', $name->toScalar());
        return Option::fromValue($qb->getQuery()->getOneOrNullResult())->getOrCall(function () use ($name) {
            $info = new DomainInfo();
            $info->setName($name);
            return $info;
        });
    }
}
