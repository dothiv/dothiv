<?php

namespace Dothiv\APIBundle\JsonLd;

use Dothiv\ValueObject\URLValue;

class JsonLdTypedEntity extends JsonLdEntity implements JsonLdTypedEntityInterface
{

    /**
     * @var URLValue
     * @Serializer\SerializedName("@type")
     */
    protected $jsonLdType;

    /**
     * @param URLValue $jsonLdType
     *
     * @return self
     */
    public function setJsonLdType(UrlValue $jsonLdType)
    {
        $this->jsonLdType = $jsonLdType;

        return $this;
    }

    /**
     * @return URLValue
     */
    public function getJsonLdType()
    {
        return $this->jsonLdType;
    }
}
