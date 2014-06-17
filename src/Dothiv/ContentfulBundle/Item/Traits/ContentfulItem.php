<?php

namespace Dothiv\ContentfulBundle\Item\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait ContentfulItem
{
    /**
     * @ORM\Column(type="json_array")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type("array")
     * @var array
     */
    private $fields = array();

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

    public function getFields()
    {
        return $this->fields;
    }
}
