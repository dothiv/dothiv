<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a request with a domain name
 */
class DomainNameRequest
{
    /**
     * Domain name
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^([a-zA-Z0-9]|xn--)(?:[a-zA-Z0-9]|-(?!-)){1,62}[a-zA-Z0-9]\.hiv$/")
     */
    public $name;
}
