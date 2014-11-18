<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a claim request
 */
class ClaimRequest extends AbstractDataModel implements DataModelInterface
{
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $token;
}
