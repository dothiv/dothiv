<?php


namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Model\VatRuleModel;
use Dothiv\BusinessBundle\Repository\CountryRepositoryInterface;
use Dothiv\ValueObject\IdentValue;

/**
 * Service for getting VAT rules to apply according to https://trello.com/c/1fOKtchM/9-vat-rules
 */
class VatRules implements VatRulesInterface
{

    /**
     * @var int
     */
    private $vatPercent;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepo;

    /**
     * @param int                        $vatPercent
     * @param CountryRepositoryInterface $countryRepo
     */
    public function __construct($vatPercent, CountryRepositoryInterface $countryRepo)
    {
        $this->countryRepo = $countryRepo;
        $this->vatPercent  = (int)$vatPercent;
    }

    /**
     * @param            $isOrg
     * @param IdentValue $country
     * @param            $hasVatNo
     *
     * @return VatRuleModel
     */
    public function getRules($isOrg, IdentValue $country, $hasVatNo)
    {
        $rules = new VatRuleModel(
            $this->getVat($isOrg, $country, $hasVatNo),
            $this->getReverseChargeNote($isOrg, $country, $hasVatNo)
        );
        return $rules;
    }

    protected function getReverseChargeNote($isOrg, IdentValue $country, $hasVatNo)
    {
        if ($isOrg
            && $this->isEu($country) && !$this->isGermany($country)
            && $hasVatNo
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param boolean    $isOrg
     * @param IdentValue $country
     * @param boolean    $hasVatNo
     *
     * @return int
     */
    protected function getVat($isOrg, IdentValue $country, $hasVatNo)
    {
        if (!$isOrg) {
            return $this->vatPercent;
        }
        if ($this->isGermany($country)) {
            return $this->vatPercent;
        }
        if ($this->isEu($country) && !$hasVatNo) {
            return $this->vatPercent;
        }
        return 0;
    }

    /**
     * @param IdentValue $country
     *
     * @return bool
     */
    protected function isGermany(IdentValue $country)
    {
        $countryOptional = $this->countryRepo->getCountryByIso($country);
        if ($countryOptional->isEmpty()) {
            return false;
        }
        return $countryOptional->get()->iso === "DE";
    }

    /**
     * @param IdentValue $country
     *
     * @return bool
     */
    protected function isEu(IdentValue $country)
    {
        $countryOptional = $this->countryRepo->getCountryByIso($country);
        if ($countryOptional->isEmpty()) {
            return false;
        }
        return $countryOptional->get()->eu;
    }
}
