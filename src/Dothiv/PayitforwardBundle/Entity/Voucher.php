<?php

namespace Dothiv\PayitforwardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\Traits\CreateUpdateTime;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Represents a voucher.
 *
 * @ORM\Entity(repositoryClass="Dothiv\PayitforwardBundle\Repository\VoucherRepository")
 * @ORM\Table(name="PayitforwardVoucher")
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="payitforward_voucher__code",columns={"code"})})
 */
class Voucher extends Entity
{
    use CreateUpdateTime;

    /**
     * The voucher code
     *
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $code;

    /**
     * The order this voucher is used for
     *
     * @ORM\ManyToOne(targetEntity="Dothiv\PayitforwardBundle\Entity\Order")
     * @ORM\JoinColumn(onDelete="RESTRICT",nullable=true)
     * @var Order|null
     */
    protected $order;

    /**
     * @return IdentValue
     */
    public function getCode()
    {
        return new IdentValue($this->code);
    }

    /**
     * @param IdentValue $code
     *
     * @return self
     */
    public function setCode(IdentValue $code)
    {
        $this->code = (string)$code;
        return $this;
    }

    /**
     * @return Order|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order = null)
    {
        $this->order = $order;
    }
} 
