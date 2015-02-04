<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Model\VatRuleModel;
use Dothiv\ValueObject\IdentValue;

interface VatRulesInterface
{
    /**
     * @param            $isOrg
     * @param IdentValue $country
     * @param            $hasVatNo
     *
     * @return VatRuleModel
     */
    public function getRules($isOrg, IdentValue $country, $hasVatNo);
}
