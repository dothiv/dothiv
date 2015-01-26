<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\Domain;

use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface;
use Dothiv\ValueObject\HivDomainValue;

abstract class AbstractDomainUserReminder
{
    /**
     * ISO codes of countries to send german version to.
     *
     * @var string[]
     */
    protected $deCountries = ['DE', 'AT', 'CH'];

    /**
     * @var DomainWhoisRepositoryInterface $domainWhoisRepo
     */
    private $domainWhoisRepo;

    /**
     * @param DomainWhoisRepositoryInterface $domainWhoisRepo
     */
    public function __construct(DomainWhoisRepositoryInterface $domainWhoisRepo)
    {
        $this->domainWhoisRepo = $domainWhoisRepo;
    }

    /**
     * @param HivDomainValue $d
     *
     * @return string
     */
    protected function getLocale(HivDomainValue $d)
    {
        $locale        = 'en';
        $whoisOptional = $this->domainWhoisRepo->findByDomain($d);
        if ($whoisOptional->isDefined()) {
            /** @var DomainWhois $whois */
            $whois = $whoisOptional->get();
            if (in_array($whois->getWhois()->get('Registrant Country'), $this->deCountries)) {
                $locale = 'de';
                return $locale;
            }
            return $locale;
        }
        return $locale;
    }

}
