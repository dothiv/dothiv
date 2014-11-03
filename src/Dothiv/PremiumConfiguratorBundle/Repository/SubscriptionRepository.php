<?php

namespace Dothiv\PremiumConfiguratorBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\PremiumConfiguratorBundle\Exception\EntityNotFoundException;
use Dothiv\PremiumConfiguratorBundle\Exception\InvalidArgumentException;
use PhpOption\Option;
use Symfony\Component\Validator\ValidatorInterface;

class SubscriptionRepository extends DoctrineEntityRepository implements SubscriptionRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function findByDomain(Domain $domain)
    {
        return Option::fromValue(
            $this->createQueryBuilder('s')
                ->andWhere('s.domain = :domain')->setParameter('domain', $domain)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * Returns the un-activated subscriptions.
     *
     * @return Subscription[]|ArrayCollection
     */
    public function findInactive()
    {
        return new ArrayCollection($this->createQueryBuilder('s')
            ->andWhere('s.active = 0')
            ->getQuery()
            ->getResult());
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Subscription $subscription)
    {
        $this->getEntityManager()->persist($this->validate($subscription));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        return Option::fromValue($this->find($id))->getOrCall(function() use($id) {
            throw new EntityNotFoundException();
        });
    }
}
