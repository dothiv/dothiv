<?php

namespace Dothiv\APIBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnprocessableEntityHttpException extends HttpException implements ExceptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(422, $message, $previous, array(), $code);
    }
}
