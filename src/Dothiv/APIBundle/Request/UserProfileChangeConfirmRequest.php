<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for confirming a user profile change
 */
class UserProfileChangeConfirmRequest extends AbstractDataModel implements DataModelInterface
{

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $confirmed;

}
