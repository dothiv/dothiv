<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a request creating a new domain collaborator.
 */
class DomainCollaboratorCreateRequest extends AbstractDataModel implements DataModelInterface
{

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $firstname; // e.g.: Jill

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $lastname; // e.g.: Jones

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Email
     */
    protected $email; // e.g.: jill@example.com

    /**
     * Domain name
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     * @var string
     */
    protected $domain;

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = strtolower($domain);
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     *
     * @return self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }
}
