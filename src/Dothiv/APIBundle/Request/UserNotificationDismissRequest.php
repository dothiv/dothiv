<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for dismissing a user notification
 */
class UserNotificationDismissRequest extends AbstractDataModel implements DataModelInterface
{

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    public $dismissed;

}
