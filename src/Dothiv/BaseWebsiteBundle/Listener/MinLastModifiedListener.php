<?php

namespace Dothiv\BaseWebsiteBundle\Listener;

use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;

/**
 * This listeners updates the config setting used by RequestLastModifiedCache to set the minLastModified date
 * if a config value is changed that affects it.
 */
class MinLastModifiedListener
{

    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepo;

    /**
     * @var ClockValue
     */
    private $clock;

    /**
     * @var string
     */
    private $keyToUpdate;

    /**
     * @var string[]
     */
    private $watches = array();

    /**
     * @param ConfigRepositoryInterface $configRepo
     * @param ClockValue                $clock
     * @param string                    $keyToUpdate
     */
    public function __construct($configRepo, $clock, $keyToUpdate)
    {
        $this->clock       = $clock;
        $this->configRepo  = $configRepo;
        $this->keyToUpdate = $keyToUpdate;
    }

    /**
     * @param string      $entity
     * @param string|null $identifier
     */
    public function addWatch($entity, $identifier)
    {
        if (Option::fromValue($identifier)->isEmpty()) {
            $this->watches[$entity] = true;
        } else {
            if (!isset($this->watches[$entity])) {
                $this->watches[$entity] = array();
            }
            $this->watches[$entity][$identifier] = true;
        }

    }

    /**
     * @param EntityChangeEvent $e
     */
    public function onEntityChange(EntityChangeEvent $e)
    {
        $entity     = $e->getChange()->getEntity();
        $identifier = $e->getChange()->getIdentifier()->toScalar();
        if (!isset($this->watches[$entity])) {
            return;
        }
        if (is_array($this->watches[$entity]) && !isset($this->watches[$entity][$identifier])) {
            return;
        }
        $config = $this->configRepo->get($this->keyToUpdate);
        $config->setValue($this->clock->getNow()->format(DATE_W3C));
        $this->configRepo->persist($config)->flush();
    }
}
