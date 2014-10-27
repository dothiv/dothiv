<?php


namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\Config;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use PhpOption\Option;

class ConfigRepository extends DoctrineEntityRepository implements ConfigRepositoryInterface, CRUDRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\PaginatedQueryTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(Config $config)
    {
        $this->getEntityManager()->persist($this->validate($config));
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
    function get($key)
    {
        return Option::fromValue($this->findOneBy(
            array('name' => $key)
        ))->getOrCall(function () use ($key) {
            $config = new Config();
            $config->setName($key);
            return $config;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated($offsetKey = null, $offsetDir = null)
    {
        return $this->buildPaginatedResult($this->createQueryBuilder('i'), $offsetKey, $offsetDir);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return Option::fromValue($this->get($identifier));
    }

    /**
     * @return string
     */
    protected function getPaginationSortField()
    {
        return 'updated';
    }

}
