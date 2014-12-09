<?php

namespace Dothiv\APIBundle\JsonLd;

use Dothiv\ValueObject\URLValue;

use JMS\Serializer\Annotation as Serializer;

interface JsonLdTypedEntityInterface extends JsonLdEntityInterface
{
    /**
     * @return UrlValue
     */
    public function getJsonLdType();
}
