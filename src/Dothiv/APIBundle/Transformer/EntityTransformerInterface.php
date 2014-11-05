<?php

namespace Dothiv\APIBundle\Transformer;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\BusinessBundle\Entity\EntityInterface;

interface EntityTransformerInterface
{
    /**
     * @param EntityInterface $entity
     * @param string          $route
     * @param boolean         $listing
     *
     * @return JsonLdEntityInterface
     */
    public function transform(EntityInterface $entity, $route = null, $listing = false);
}
