<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a user creation request
 */
class UserCreateRequest extends LoginLinkRequest implements DataModelInterface
{
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $firstname;

    /*
    * @var string
    * @Assert\NotNull
    * @Assert\NotBlank
    */
    public $surname;
}
