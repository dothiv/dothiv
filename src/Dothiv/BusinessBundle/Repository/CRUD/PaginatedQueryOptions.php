<?php


namespace Dothiv\BusinessBundle\Repository\CRUD;

use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;

class PaginatedQueryOptions
{

    /**
     * @var IdentValue
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
        if (!is_scalar($offsetKey)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Offset key is not a string, but %s', gettype($offsetKey)
                )
            );
        }
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
     * @return Option of IdentValue
     */
    public function getSortField()
    {
        return Option::fromValue($this->sortField);
    }

    /**
     * @param IdentValue $sortField
     *
     * @return self
     */
    public function setSortField(IdentValue $sortField)
    {
        $this->sortField = $sortField;
        return $this;
    }

}
