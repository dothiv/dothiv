<?php

namespace Dothiv\RegistryWebsiteBundle\Request;

use Dothiv\APIBundle\Request\DomainNameRequest;
use Symfony\Component\Validator\Constraints as Assert;

class PremiumBidPostRequest extends DomainNameRequest
{
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $firstname; // e.g.: Jill

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $surname; // e.g.: Jones
} 
