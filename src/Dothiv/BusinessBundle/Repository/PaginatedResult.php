<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PhpOption\Option;

class PaginatedResult
{
    /**
     * @var ArrayCollection
     */
    protected $result;

    /**
     * @var Option
     */
    protected $nextPageKey;

    /**
     * @var Option
     */
    protected $prevPageKey;

    /**
     * @var int
     */
    protected $itemsPerPage;

    /**
     * @var int
     */
    protected $total;

    /**
     * @param int $itemsPerPage
     * @param int $total
     */
    public function __construct($itemsPerPage, $total)
    {
        $this->result = new ArrayCollection();
        $this->setItemsPerPage($itemsPerPage);
        $this->setTotal($total);
    }

    /**
     * @return Option
     */
    public function getNextPageKey()
    {
        return Option::fromValue($this->nextPageKey);
    }

    /**
     * @return Option
     */
    public function getPrevPageKey()
    {
        return Option::fromValue($this->prevPageKey);
    }

    /**
     * @param ArrayCollection $result
     *
     * @return self
     */
    public function setResult(ArrayCollection $result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param int $itemsPerPage
     *
     * @return self
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Sets the nextPageKey from the result using $keyFunc to extract the key,
     *
     * @param callable $keyFunc
     *
     * @return self
     */
    public function setNextPageKey(callable $keyFunc = null)
    {
        $this->nextPageKey = $keyFunc($this->getResult()->last());
        return $this;
    }

    /**
     * Sets the prevPageKey from the result using $keyFunc to extract the key,
     *
     * @param callable $keyFunc
     *
     * @return self
     */
    public function setPrevPageKey(callable $keyFunc)
    {
        $this->prevPageKey = $keyFunc($this->getResult()->first());
        return $this;
    }

    /**
     * @param int $total
     *
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = (int)$total;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}
