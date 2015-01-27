<?php

namespace Dothiv\BusinessBundle\Model;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use PhpOption\None;
use PhpOption\Option;

class FilterQuery
{

    /**
     * @var string
     */
    private $term = null;

    /**
     * @var FilterQueryProperty[]
     */
    private $properties = array();

    /**
     * If set, the result should be limited to entries accessible to the given user
     *
     * @var User
     */
    private $user;

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
     * Returns the first value for the given property
     *
     * @param string $name
     *
     * @return Option of FilterQueryProperty
     *
     * @deprecated Use getProperty()
     */
    public function getSingleProperty($name)
    {
        return isset($this->properties[$name]) ? Option::fromValue(is_array($this->properties[$name]) ? $this->properties[$name][0] : $this->properties[$name]) : None::create();
    }

    /**
     * @param string $name
     *
     * @return Option of FilterQueryProperty[]
     */
    public function getProperty($name)
    {
        return isset($this->properties[$name]) ? Option::fromValue($this->properties[$name]) : None::create();
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $operator
     */
    public function setProperty($name, $value, $operator = null)
    {
        if (!isset($this->properties[$name])) {
            $this->properties[$name] = [];
        }
        $this->properties[$name][] = new FilterQueryProperty($name, $value, $operator);
    }

    /**
     * @return Option of User
     */
    public function getUser()
    {
        return Option::fromValue($this->user);
    }

    /**
     * @param User|null $user
     *
     * @return self
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        return $this;
    }
}
