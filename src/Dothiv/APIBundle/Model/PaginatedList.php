<?php

namespace Dothiv\APIBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;

class PaginatedList implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var UrlValue
     */
    protected $nextPageUrl;

    /**
     * @var UrlValue
     */
    protected $prevPageUrl;

    /**
     * @var int
     */
    protected $itemsPerPage;

    /**
     * @var int
     */
    protected $total;

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/PaginatedList'));
    }

    /**
     * @param mixed $item
     *
     * @return self
     */
    public function addItem($item)
    {
        if ($this->items === null) {
            $this->items = array();
        }
        $this->items[] = $item;
        return $this;
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
     * @param URLValue $nextPageUrl
     *
     * @return self
     */
    public function setNextPageUrl(URLValue $nextPageUrl)
    {
        $this->nextPageUrl = $nextPageUrl;
        return $this;
    }

    /**
     * @return URLValue
     */
    public function getNextPageUrl()
    {
        return $this->nextPageUrl;
    }

    /**
     * @param URLValue $prevPageUrl
     *
     * @return self
     */
    public function setPrevPageUrl(URLValue $prevPageUrl)
    {
        $this->prevPageUrl = $prevPageUrl;
        return $this;
    }

    /**
     * @return URLValue
     */
    public function getPrevPageUrl()
    {
        return $this->prevPageUrl;
    }

    /**
     * @return int
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("count")
     */
    public function count()
    {
        return count($this->items);
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
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
