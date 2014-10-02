<?php

namespace Dothiv\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Represents an invoice.
 *
 * NOTE: As we don't need multiple items per invoice, they currently can only have one (1) item.
 *
 * @ORM\Entity(repositoryClass="Dothiv\BusinessBundle\Repository\InvoiceRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Assert\Callback(methods={"isTotalValid", "isVatValid"})
 */
class Invoice extends Entity
{
    use Traits\CreateUpdateTime;

    /**
     * @ORM\Column(type="string",nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $fullname;

    /**
     * @ORM\Column(type="string",nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $address1;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $address2;

    /**
     * @ORM\Column(type="string",nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $country;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $vatNo;

    /**
     * @ORM\Column(type="string",nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $itemDescription;

    /**
     * @ORM\Column(type="integer",nullable=false)
     * @var int
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type("integer")
     * @Serializer\Expose
     */
    protected $itemPrice = 0;

    /**
     * @ORM\Column(type="integer",nullable=false)
     * @var int
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type("integer")
     * @Serializer\Expose
     */
    protected $vatPrice = 0;

    /**
     * @ORM\Column(type="integer",nullable=false)
     * @var int
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type("integer")
     * @Serializer\Expose
     */
    protected $vatPercent = 0;

    /**
     * @ORM\Column(type="integer",nullable=false)
     * @var int
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type("integer")
     * @Serializer\Expose
     */
    protected $totalPrice = 0;

    /**
     * @return string
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("no")
     */
    public function getNo()
    {
        return sprintf('W%s-%d', $this->getCreated()->format('Ymd'), $this->getId());
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     *
     * @return self
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     *
     * @return self
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param string $fullname
     *
     * @return self
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
        return $this;
    }

    /**
     * @return string
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     * @param string $itemDescription
     *
     * @return self
     */
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * @param int $itemPrice
     *
     * @return self
     */
    public function setItemPrice($itemPrice)
    {
        $this->itemPrice = $itemPrice;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param int $totalPrice
     *
     * @return self
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    /**
     * @return string
     */
    public function getVatNo()
    {
        return $this->vatNo;
    }

    /**
     * @param string $vatNo
     *
     * @return self
     */
    public function setVatNo($vatNo)
    {
        $this->vatNo = $vatNo;
        return $this;
    }

    /**
     * @return int
     */
    public function getVatPercent()
    {
        return $this->vatPercent;
    }

    /**
     * @param int $vatPercent
     *
     * @return self
     */
    public function setVatPercent($vatPercent)
    {
        $this->vatPercent = $vatPercent;
        return $this;
    }

    /**
     * @return int
     */
    public function getVatPrice()
    {
        return $this->vatPrice;
    }

    /**
     * @param int $vatPrice
     *
     * @return self
     */
    public function setVatPrice($vatPrice)
    {
        $this->vatPrice = $vatPrice;
        return $this;
    }

    /**
     * Validates the total price
     *
     * @param ExecutionContextInterface $context
     */
    public function isTotalValid(ExecutionContextInterface $context)
    {
        $expected = $this->getItemPrice() + $this->getVatPrice();
        $actual   = $this->getTotalPrice();
        if ($expected != $actual) {
            $context->addViolationAt('totalPrice', 'Expected value to be %expected%, but it is %actual%!', array(
                '%expected%' => $expected,
                '%actual%'   => $actual
            ), null);
        }
    }

    /**
     * Validates the vat price
     *
     * @param ExecutionContextInterface $context
     */
    public function isVatValid(ExecutionContextInterface $context)
    {
        $expected = (int)($this->getItemPrice() * $this->getVatPercent() / 100);
        $actual   = $this->getVatPrice();
        if ($expected != $actual) {
            $context->addViolationAt('vatPrice', 'Expected value to be %expected%, but it is %actual%!', array(
                '%expected%' => $expected,
                '%actual%'   => $actual
            ), null);
        }
    }
}
