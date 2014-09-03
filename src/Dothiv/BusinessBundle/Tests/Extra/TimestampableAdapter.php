<?php


namespace Dothiv\BusinessBundle\Tests\Extra;

use Gedmo\Mapping\Event\Adapter\ORM as BaseAdapterORM;
use Gedmo\Timestampable\Mapping\Event\TimestampableAdapter as TimestampableListenerInterface;

class TimestampableAdapter extends BaseAdapterORM implements TimestampableListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDateValue($meta, $field)
    {
        return new \DateTime("2014-01-02T13:14:15");
    }
}
