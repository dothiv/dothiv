<?php

namespace Dothiv\APIBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotImplementedHttpException extends HttpException implements ExceptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(501, $message, $previous, array(), $code);
    }
}
