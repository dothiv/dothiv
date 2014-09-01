<?php


namespace Dothiv\AfiliasImporterBundle\Service;

use Dothiv\AfiliasImporterBundle\Model\PaginatedList;

interface AfiliasImporterServiceInterface
{
    /**
     * Returns the domain registrations.
     *
     * @param string $url
     *
     * @return PaginatedList
     */
    public function getRegistrations($url);
} 
