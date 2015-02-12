<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for recovering a password
 */
class RecoverPasswordRequest extends AbstractDataModel implements DataModelInterface
{

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $email;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $password;

}
