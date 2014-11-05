<?php

namespace Dothiv\APIBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;

class UserNotificationModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    /**
     * @var array
     */
    private $properties;

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/UserNotification'));
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     *
     * @return self
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
        return $this;
    }
}
