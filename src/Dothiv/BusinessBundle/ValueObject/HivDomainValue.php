<?php

namespace Dothiv\BusinessBundle\ValueObject;

use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class HivDomainValue implements StringValue
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var string[]
     */
    private $parts;

    /**
     * @param string $domain
     *
     * @throws InvalidArgumentException If an invalid url is provided.
     */
    public function __construct($domain)
    {
        if ($domain instanceof HivDomainValue) {
            $this->domain = (string)$domain;
            return;
        }

        $domain = trim(strtolower($domain));
        $regexp = "/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/";
        if (!preg_match($regexp, $domain)) {
            throw new InvalidArgumentException(sprintf('Invalid hiv domain provided: "%s"!', $domain));
        }

        $this->domain = $domain;
        list($secondLevel,) = explode('.', $domain);
        $this->parts = array(
            'secondLevel' => $secondLevel
        );
    }

    /**
     * Static constructor.
     *
     * @param string $domain
     *
     * @return HivDomainValue
     */
    public static function create($domain)
    {
        $c = __CLASS__;
        return new $c($domain);
    }

    /**
     * Converts the value to a string.
     *
     * @return string
     * @Serializer\HandlerCallback("json", direction = "serialization")
     */
    public function __toString()
    {
        return $this->domain;
    }

    /**
     * @return null|string
     */
    public function getSecondLevel()
    {
        return $this->getPart('secondLevel');
    }

    /**
     * @param string $part The part of the url to return.
     *
     * @return null|string
     */
    public function getPart($part)
    {
        return isset($this->parts[$part]) ? $this->parts[$part] : null;
    }

    /**
     * Returns the UTF8 representation of the domain.
     *
     * @return string
     */
    public function toUTF8()
    {
        return idn_to_utf8($this->domain);
    }
} 
