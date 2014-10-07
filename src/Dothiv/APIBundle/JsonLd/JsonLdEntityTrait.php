<?php

namespace Dothiv\APIBundle\JsonLd;

use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;

trait JsonLdEntityTrait
{
    /**
     * @var URLValue
     * @Serializer\SerializedName("@context")
     */
    protected $jsonLdContext;

    /**
     * @var URLValue
     * @Serializer\SerializedName("@id")
     */
    protected $jsonLdId;

    /**
     * @param URLValue $jsonLdContext
     *
     * @return self
     */
    public function setJsonLdContext(UrlValue $jsonLdContext)
    {
        $this->jsonLdContext = $jsonLdContext;

        return $this;
    }

    /**
     * @return URLValue
     */
    public function getJsonLdContext()
    {
        return $this->jsonLdContext;
    }

    /**
     * @param URLValue $jsonLdId
     *
     * @return self
     */
    public function setJsonLdId(URLValue $jsonLdId)
    {
        $this->jsonLdId = $jsonLdId;
    }

    /**
     * @return URLValue
     */
    public function getJsonLdId()
    {
        return $this->jsonLdId;
    }

}
