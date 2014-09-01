<?php

namespace Dothiv\BusinessBundle\ValueObject;

use Dothiv\BusinessBundle\Exception\InvalidArgumentException;

class IdentValue implements StringValue
{
    private $ident;

    /**
     * @param string $ident
     *
     * @throws InvalidArgumentException
     */
    public function __construct($ident)
    {
        $ident = (string)$ident;
        $regexp = '/^[A-Za-z0-9_\.-]{1,255}$/';
        if (!preg_match($regexp, $ident)) {
            throw new InvalidArgumentException(sprintf('Invalid ident value provided: "%s"!', $ident));
        }
        $this->ident = $ident;
    }

    /**
     * Static constructor.
     *
     * @param string $ident
     *
     * @return HexValue
     */
    public static function create($ident)
    {
        $c = __CLASS__;
        return new $c($ident);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->ident;
    }
}
