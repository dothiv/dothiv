<?php

namespace Dothiv\ContentfulBundle\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OutputInterfaceLogger implements LoggerInterface
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    /**
     *{@inheritdoc}
     */
    public function error($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = array())
    {
        $this->output->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        $this->output->writeln($message);
    }
}
