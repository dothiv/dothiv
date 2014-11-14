<?php

namespace Dothiv\APIBundle\Model\Traits;

use Dothiv\ValueObject\W3CDateTimeValue;

trait W3CCreatedTrait
{

    /**
     * @var W3CDateTimeValue
     */
    protected $created;

    /**
     * @param W3CDateTimeValue $created
     *
     * @return self
     */
    public function setCreated(W3CDateTimeValue $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return W3CDateTimeValue
     */
    public function getCreated()
    {
        return $this->created;
    }
}
