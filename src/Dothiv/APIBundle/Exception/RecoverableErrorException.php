<?php


namespace Dothiv\APIBundle\Exception;

class RecoverableErrorException extends \ErrorException implements ExceptionInterface
{

    /**
     * an array that points to the active symbol table at the
     * point the error occurred
     *
     * @var array
     */
    private $context;

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     *
     * @return self
     */
    public function setContext(array $context)
    {
        $this->context = $context;
        return $this;
    }

}
