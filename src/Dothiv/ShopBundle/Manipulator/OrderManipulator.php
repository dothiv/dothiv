<?php

namespace Dothiv\ShopBundle\Manipulator;

use Dothiv\APIBundle\Manipulator\EntityManipulatorInterface;
use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Exception\InvalidArgumentException;
use Dothiv\ShopBundle\Request\OrderCreateRequest;

class OrderManipulator implements EntityManipulatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function manipulate(EntityInterface $entity, DataModelInterface $data)
    {
        if (!($data instanceof OrderCreateRequest)) {
            throw new InvalidArgumentException(
                sprintf('Expected $data to be a OrderCreateRequest, got "%s"!', get_class($data))
            );
        }
        if (!($entity instanceof Order)) {
            throw new InvalidArgumentException(
                sprintf('Expected $entity to be a Order, got "%s"!', get_class($entity))
            );
        }
        $entity->setDomain($data->getDomain());
        $entity->setClickCounter($data->getClickCounter());
        $entity->setRedirect($data->getRedirect());
        $entity->setDuration($data->getDuration());
        $entity->setFirstname($data->getFirstname());
        $entity->setLastname($data->getLastname());
        $entity->setEmail($data->getEmail());
        $entity->setPhone($data->getPhone());
        $entity->setFax($data->getFax());
        $entity->setLocality($data->getLocality());
        $entity->setLocality2($data->getLocality2());
        $entity->setCity($data->getCity());
        $entity->setCountry($data->getCountry());
        $entity->setOrganization($data->getOrganization());
        $entity->setVatNo($data->getVatNo());
        $entity->setStripeToken($data->getStripeToken());
        $entity->setStripeCard($data->getStripeCard());
    }
}


