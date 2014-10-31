<?php


namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use PhpOption\Option;

class PaginatedQueryOptions
{

    /**
     * @var string
     */
    private $sortField = null;

    /**
     * @var string
     */
    private $sortDir = null;

    /**
     * @var string
     */
    private $offsetKey = null;

    /**
     * @return Option of string
     */
    public function getOffsetKey()
    {
        return Option::fromValue($this->offsetKey);
    }

    /**
     * @param string $offsetKey
     *
     * @return self
     */
    public function setOffsetKey($offsetKey)
    {
        $this->offsetKey = $offsetKey;
        return $this;
    }

    /**
     * @return Option of string
     */
    public function getSortDir()
    {
        return Option::fromValue($this->sortDir);
    }

    /**
     * @param string $sortDir
     *
     * @return self
     */
    public function setSortDir($sortDir)
    {
        if (!in_array($sortDir, array('asc', 'desc'))) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid sort dir: "%s"', $sortDir
                )
            );
        }
        $this->sortDir = $sortDir;
        return $this;
    }

    /**
     * @return Option of string
     */
    public function getSortField()
    {
        return Option::fromValue($this->sortField);
    }

    /**
     * @param string $sortField
     *
     * @return self
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;
        return $this;
    }

} 
