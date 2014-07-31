<?php

namespace Dothiv\PremiumConfiguratorBundle\Repository;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\PremiumConfiguratorBundle\Exception\InvalidArgumentException;
use PhpOption\Option;
use Symfony\Component\Validator\ValidatorInterface;

class PremiumBannerRepository extends DoctrineEntityRepository implements PremiumBannerRepositoryInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * {@inheritdoc}
     */
    public function findByBanner(Banner $banner)
    {
        return Option::fromValue(
            $this->createQueryBuilder('p')
                ->andWhere('p.banner = :banner')->setParameter('banner', $banner)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function persist(PremiumBanner $premiumBanner)
    {
        $errors = $this->validator->validate($premiumBanner);
        if (count($errors) != 0) {
            throw new InvalidArgumentException((string)$errors);
        }
        $this->getEntityManager()->persist($premiumBanner);
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
