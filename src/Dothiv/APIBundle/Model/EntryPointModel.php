<?php

namespace Dothiv\APIBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\APIBundle\JsonLd\JsonLdTypedEntityInterface;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;
use PhpOption\None;
use PhpOption\Option;

class EntryPointModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    /**
     * @var JsonLdEntityInterface[]
     */
    protected $links = array();

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/EntryPoint'));
    }

    /**
     * @param URLValue              $url
     * @param JsonLdEntityInterface $entity
     */
    public function addLink(URLValue $url, JsonLdEntityInterface $entity)
    {
        $this->links[$url->toScalar()] = $entity;
    }

    /**
     * @param URLValue $context
     * @param URLValue $type
     *
     * @return Option of JsonLdEntityInterface
     */
    public function getLink(URLValue $context, URLValue $type = null)
    {
        foreach ($this->links as $link) {
            if ($link->getJsonLdContext()->equals($context)) {
                if ($type == null) {
                    return Option::fromValue($link);
                } else {
                    if ($link instanceof JsonLdTypedEntityInterface) {
                        if ($link->getJsonLdType()->equals($type)) {
                            return Option::fromValue($link);
                        }
                    }
                }
            }
        }
        return None::create();
    }
}
