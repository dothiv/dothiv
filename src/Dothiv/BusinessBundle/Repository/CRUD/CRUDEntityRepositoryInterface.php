<?php


namespace Dothiv\BusinessBundle\Repository\CRUD;

/**
 * Implements all CRUD Interfaces.
 */
interface CRUDEntityRepositoryInterface extends PaginatedReadEntityRepositoryInterface, CreateEntityRepositoryInterface, DeleteEntityRepositoryInterface
{
}
