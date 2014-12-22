<?php


namespace Dothiv\BusinessBundle\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;

class ConfigRepository extends DoctrineEntityRepository implements ConfigRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\PaginatedQueryTrait;
    use Traits\GetItemEntityName;

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
    public function persistItem(EntityInterface $item)
    {
        $this->persist($item);
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
    public function getPaginated(CRUD\PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        if ($options->getSortField()->isEmpty()) {
            $options->setSortField(new IdentValue('name'));
            $options->setSortDir('asc');
        }
        return $this->buildPaginatedResult($this->createQueryBuilder('i'), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return Option::fromValue($this->get($identifier));
    }
}
