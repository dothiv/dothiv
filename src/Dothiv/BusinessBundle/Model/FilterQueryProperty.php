<?php


namespace Dothiv\BusinessBundle\Model;

use Dothiv\BusinessBundle\Exception\InvalidArgumentException;

class FilterQueryProperty
{
    /**
     * @const string
     */
    const OPERATOR_EQUALS = '=';

    /**
     * @const string
     */
    const OPERATOR_NOT_EQUALS = '!=';

    /**
     * @const string
     */
    const OPERATOR_GREATER_THAN = '>';

    /**
     * @const string
     */
    const OPERATOR_LESS_THAN = '<';

    /**
     * @const string
     */
    const OPERATOR_LESS_OR_EQUAL_THAN = '<=';

    /**
     * @const string
     */
    const OPERATOR_GREATER_OR_EQUAL_THAN = '>=';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $operator;

    /**
     * @param string $name
     * @param string $value
     * @param string $operator
     */
    public function __construct($name, $value, $operator = null)
    {
        $this->name  = $name;
        $this->value = $value;
        if ($operator === null) {
            $this->operator = static::OPERATOR_EQUALS;
        } else {
            $ops = [
                static::OPERATOR_EQUALS,
                static::OPERATOR_NOT_EQUALS,
                static::OPERATOR_GREATER_THAN,
                static::OPERATOR_LESS_THAN,
                static::OPERATOR_GREATER_OR_EQUAL_THAN,
                static::OPERATOR_LESS_OR_EQUAL_THAN
            ];
            if (!in_array($operator, $ops)) {
                throw new InvalidArgumentException(
                    sprintf('Invalid operator "%s"!', $operator)
                );
            }
            $this->operator = $operator;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function equals()
    {
        return $this->operator === static::OPERATOR_EQUALS;
    }

    /**
     * @return bool
     */
    public function notEquals()
    {
        return $this->operator === static::OPERATOR_NOT_EQUALS;
    }

    /**
     * @return bool
     */
    public function greaterThan()
    {
        return $this->operator === static::OPERATOR_GREATER_THAN;
    }

    /**
     * @return bool
     */
    public function lessThan()
    {
        return $this->operator === static::OPERATOR_LESS_THAN;
    }

    /**
     * @return bool
     */
    public function greaterOrEqualThan()
    {
        return $this->operator === static::OPERATOR_GREATER_OR_EQUAL_THAN;
    }

    /**
     * @return bool
     */
    public function lessOrEqualThan()
    {
        return $this->operator === static::OPERATOR_LESS_OR_EQUAL_THAN;
    }
}
