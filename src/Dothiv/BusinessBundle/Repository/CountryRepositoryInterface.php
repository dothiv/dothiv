<?php


namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Model\CountryModel;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;

interface CountryRepositoryInterface
{
    /**
     * @return ArrayCollection|CountryModel[]
     */
    public function getCountries();

    /**
     * @param IdentValue $iso
     *
     * @return Option of CountryModel
     */
    public function getCountryByIso(IdentValue $iso);
}
