<?php

namespace Dothiv\BusinessBundle\ValueObject;

use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class HexValue implements StringValue
{
    private $hex;

    /**
     * @param string $hex
     *
     * @throws InvalidArgumentException
     */
    public function __construct($hex)
    {
        $stringHex = strtoupper((string)$hex);
        if (strlen($stringHex) == 4) {
            $stringHex = '#' . str_repeat($stringHex[1], 2) . str_repeat($stringHex[2], 2) . str_repeat($stringHex[3], 2);
        }
        $regexp = '/^#[A-F0-9]{6}$/';
        if (!preg_match($regexp, $stringHex)) {
            throw new InvalidArgumentException(sprintf('Invalid hex value provided: "%s"!', $hex));
        }
        $this->hex = $stringHex;
    }

    /**
     * Static constructor.
     *
     * @param string $hex
     *
     * @return HexValue
     */
    public static function create($hex)
    {
        $c = __CLASS__;
        return new $c($hex);
    }

    /**
     * {@inheritdoc}
     * @Serializer\HandlerCallback("json", direction = "serialization")
     */
    public function __toString()
    {
        return $this->hex;
    }
}
