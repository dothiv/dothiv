<?php


namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Model\CountryModel;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;

class CountryRepository implements CountryRepositoryInterface
{

    /**
     * @var ArrayCollection|CountryModel[]
     */
    private $countries;

    /**
     * @return ArrayCollection|CountryModel[]
     */
    public function getCountries()
    {
        if ($this->countries == null) {
            $this->countries = new ArrayCollection();
            foreach (json_decode(file_get_contents(__DIR__ . '/../../BaseWebsiteBundle/Resources/public/data/countries-en.json')) as $countryData) {
                $country       = new CountryModel();
                $country->iso  = $countryData[0];
                $country->name = $countryData[1];
                $country->eu   = $countryData[2];
                $this->countries->add($country);
            }
        }
        return $this->countries;
    }

    /**
     * @param IdentValue $iso
     *
     * @return Option of CountryModel
     */
    public function getCountryByIso(IdentValue $iso)
    {
        return Option::fromValue($this->getCountries()->filter(function (CountryModel $country) use ($iso) {
            return $country->iso === $iso->toScalar();
        })->first());
    }
}
