<?php


namespace Dothiv\AfiliasImporterBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ValueObject\URLValue;

class PaginatedList
{
    /**
     * @var URLValue
     */
    private $nextUrl;

    /**
     * @var ArrayCollection
     */
    private $items;

    /**
     * @var int
     */
    private $total;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @param URLValue $nextUrl
     *
     * @return self
     */
    public function setNextUrl(URLValue $nextUrl)
    {
        $this->nextUrl = $nextUrl;
        return $this;
    }

    /**
     * @return URLValue
     */
    public function getNextUrl()
    {
        return $this->nextUrl;
    }

    /**
     * @param ArrayCollection $items
     *
     * @return self
     */
    public function setItems(ArrayCollection $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param int $total
     *
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $item
     *
     * @return self
     */
    public function add($item)
    {
        $this->items->add($item);
        return $this;
    }
}
