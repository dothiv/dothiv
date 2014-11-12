<?php

namespace Dothiv\BusinessBundle\Exception;

class TemporarilyUnavailableException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var \DateTime
     */
    private $retryTime;

    public function __construct(\DateTime $retryTime)
    {
        $this->retryTime = $retryTime;
    }

    /**
     * @return \DateTime
     */
    public function getRetryTime()
    {
        return $this->retryTime;
    }
}
