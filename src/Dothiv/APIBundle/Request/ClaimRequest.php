<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a claim request
 */
class ClaimRequest
{
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/.{3,}\.hiv$/")
     */
    public $domain;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $token;
}
