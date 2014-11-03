<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;

/**
 * This repository contains the Invoices.
 */
interface InvoiceRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param Invoice $invoice
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(Invoice $invoice);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param int $id
     *
     * @return Invoice
     *
     * @throws EntityNotFoundException if order is not found.
     */
    public function getById($id);
}
