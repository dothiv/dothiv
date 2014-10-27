<?php


namespace Dothiv\AfiliasImporterBundle\Service;

use Dothiv\AfiliasImporterBundle\Exception\ServiceException;
use Dothiv\ValueObject\URLValue;

interface AfiliasImporterServiceInterface
{
    /**
     * Fetch domain registrations from url
     *
     * @param URLValue $url
     *
     * @return URLValue Next Url
     *
     * @throws ServiceException
     */
    public function fetchRegistrations(URLValue $url);

    /**
     * Fetch domain transactions from url
     *
     * @param URLValue $url
     *
     * @return URLValue Next Url
     *
     * @throws ServiceException
     */
    public function fetchTransactions(URLValue $url);
} 
