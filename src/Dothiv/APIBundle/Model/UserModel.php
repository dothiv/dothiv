<?php

namespace Dothiv\APIBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;

class UserModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $surname;

    /**
     * @var EmailValue
     */
    protected $email;

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/User'));
    }

    /**
     * @param EmailValue $email
     *
     * @return self
     */
    public function setEmail(EmailValue $email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return EmailValue
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstname
     *
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $surname
     *
     * @return self
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
}
