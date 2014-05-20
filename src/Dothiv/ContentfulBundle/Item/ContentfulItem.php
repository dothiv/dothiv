<?php

namespace Dothiv\ContentfulBundle\Item;

class ContentfulItem
{
    /**
     * @var ContentfulItem
     */
    public $sys;

    /**
     * @var array
     */
    public $fields = array();

    public function __construct()
    {
        $this->sys = new ContentfulSys();
    }

    public function __get($key)
    {
        return $this->fields[$key];
    }

    public function __set($key, $value)
    {
        $this->fields[$key] = $value;
        return $this;
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->fields);
    }
}
