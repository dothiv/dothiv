<?php

namespace Dothiv\APIBundle\JsonLd;

use Dothiv\BusinessBundle\ValueObject\URLValue;

use JMS\Serializer\Annotation as Serializer;

interface JsonLdEntityInterface
{
    /**
     * @param URLValue $jsonLdContext
     *
     * @return self
     */
    public function setJsonLdContext(UrlValue $jsonLdContext);

    /**
     * @return UrlValue
     */
    public function getJsonLdContext();

    /**
     * @param URLValue $jsonLdId
     *
     * @return self
     */
    public function setJsonLdId(URLValue $jsonLdId);

    /**
     * @return URLValue
     */
    public function getJsonLdId();
}
