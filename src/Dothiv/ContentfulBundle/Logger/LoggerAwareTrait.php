<?php

namespace Dothiv\ContentfulBundle\Logger;

use PhpOption\Option;
use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function log()
    {
        $args = func_get_args();
        Option::fromValue($this->logger)->map(function (LoggerInterface $logger) use ($args) {
            $logger->debug(call_user_func_array('sprintf', $args));
        });
    }
} 
