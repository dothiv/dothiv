<?php

namespace Dothiv\APIBundle\Model\Traits;

use Dothiv\ValueObject\W3CDateTimeValue;

trait W3CUpdatedTrait
{

    /**
     * @var W3CDateTimeValue
     */
    protected $updated;

    /**
     * @param W3CDateTimeValue $updated
     *
     * @return self
     */
    public function setUpdated(W3CDateTimeValue $updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return W3CDateTimeValue
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
