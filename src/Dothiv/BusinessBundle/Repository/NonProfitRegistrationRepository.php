<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use PhpOption\Option;
use Symfony\Component\Validator\ValidatorInterface;

class NonProfitRegistrationRepository extends DoctrineEntityRepository implements NonProfitRegistrationRepositoryInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * {@inheritdoc}
     */
    public function persist(NonProfitRegistration $nonProfitRegistration)
    {
        $errors = $this->validator->validate($nonProfitRegistration);
        if (count($errors) != 0) {
            throw new InvalidArgumentException((string)$errors);
        }
        $this->getEntityManager()->persist($nonProfitRegistration);
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
     * @param string $domain
     *
     * @return Option
     */
    public function getNonProfitRegistrationByDomainName($domain)
    {
        return Option::fromValue($this->createQueryBuilder('r')
            ->andWhere('r.domain = :domain')->setParameter('domain', $domain)
            ->getQuery()
            ->getOneOrNullResult());
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

    /**
     * Returns a list of unconfirmed NonProfitRegistrations.
     *
     * @return NonProfitRegistration[]|ArrayCollection
     */
    public function getUnconfirmed()
    {
        return new ArrayCollection($this->createQueryBuilder('r')
            ->andWhere('r.receiptSent IS NULL')
            ->getQuery()
            ->getResult());
    }
}
