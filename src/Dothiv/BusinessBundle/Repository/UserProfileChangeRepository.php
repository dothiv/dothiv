<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\Traits;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use PhpOption\Option;

class UserProfileChangeRepository extends DoctrineEntityRepository implements UserProfileChangeRepositoryInterface
{
    use Traits\PaginatedQueryTrait;
    use Traits\ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(UserProfileChange $UserProfileChange)
    {
        $this->getEntityManager()->persist($this->validate($UserProfileChange));
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
}
