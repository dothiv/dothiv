<?php


namespace Dothiv\AfiliasImporterBundle\Service;

use Dothiv\ValueObject\URLValue;

interface AfiliasImporterServiceInterface
{
    /**
     * Fetch domain registrations from url
     *
     * @param URLValue $url
     *
     * @return URLValue Next Url
     */
    public function fetchRegistrations(URLValue $url);
} 
