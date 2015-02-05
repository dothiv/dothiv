<?php


namespace Dothiv\ShopBundle\Event;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\ShopBundle\Entity\Order;
use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event
{

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Domain
     */
    private $domain;

    /**
     * @param Order  $order
     * @param Domain $domain
     */
    public function __construct(Order $order, Domain $domain)
    {
        $this->order  = $order;
        $this->domain = $domain;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
