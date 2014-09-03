<?php

namespace Dothiv\BusinessBundle\ValueObject;

use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class EmailValue implements StringValue
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string[]
     */
    private $parts;

    /**
     * @param string $email
     *
     * @throws InvalidArgumentException If an invalid url is provided.
     */
    public function __construct($email)
    {
        if ($email instanceof EmailValue) {
            $this->email = (string)$email;
            return;
        }

        $valid = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$valid) {
            throw new InvalidArgumentException(sprintf('Invalid email value provided: "%s"!', $email));
        }
        $this->email = $email;
        list($user, $host) = explode('@', $email);
        if (strpos($user, '+')) {
            list($user, $extension) = explode('+', $user);
        } else {
            $extension = null;
        }
        $this->parts = array(
            'user'      => $user,
            'extension' => $extension,
            'host'      => $host,
        );
    }

    /**
     * Static constructor.
     *
     * @param string $email
     *
     * @return EmailValue
     */
    public static function create($email)
    {
        $c = __CLASS__;
        return new $c($email);
    }

    /**
     * Converts the value to a string.
     *
     * @return string
     * @Serializer\HandlerCallback("json", direction = "serialization")
     */
    public function __toString()
    {
        return $this->email;
    }

    /**
     * Returns the emails hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->getPart('host');
    }

    /**
     * Returns the emails user.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->getPart('user');
    }

    /**
     * Returns the emails extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->getPart('extension');
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
} 
