<?php

namespace Dothiv\BusinessBundle\Model;

use PhpOption\None;
use PhpOption\Option;

class FilterQuery
{

    /**
     * @var string
     */
    private $term = null;

    /**
     * @var string[]
     */
    private $properties = array();

    /**
     * @return Option of string
     */
    public function getTerm()
    {
        return Option::fromValue($this->term);
    }

    /**
     * @param string $term
     *
     * @return self
     */
    public function setTerm($term)
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return Option of string
     */
    public function getProperty($name)
    {
        return isset($this->properties[$name]) ? Option::fromValue($this->properties[$name]) : None::create();
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }

} 
