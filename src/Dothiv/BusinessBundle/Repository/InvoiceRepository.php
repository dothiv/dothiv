<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\Invoice;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use PhpOption\Option;

class InvoiceRepository extends DoctrineEntityRepository implements InvoiceRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(Invoice $invoice)
    {
        $this->getEntityManager()->persist($this->validate($invoice));
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
     * @param int $id
     *
     * @return Invoice
     *
     * @throws EntityNotFoundException if order is not found.
     */
    public function getById($id)
    {
        return Option::fromValue($this->find($id))->getOrCall(function () use ($id) {
            throw new EntityNotFoundException();
        });
    }
}
