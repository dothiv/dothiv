<?php

namespace Dothiv\PremiumConfiguratorBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\PremiumConfiguratorBundle\Exception\InvalidArgumentException;
use PhpOption\Option;
use Symfony\Component\Validator\ValidatorInterface;

class SubscriptionRepository extends DoctrineEntityRepository implements SubscriptionRepositoryInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

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
        $errors = $this->validator->validate($subscription);
        if (count($errors) != 0) {
            throw new InvalidArgumentException((string)$errors);
        }
        $this->getEntityManager()->persist($subscription);
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
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }
}
