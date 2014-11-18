<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a claim request
 */
class LoginLinkRequest extends AbstractDataModel implements DataModelInterface
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
     * @Assert\Regex("/^(de|en|ky)$/")
     */
    public $locale;

    /**
     * @var string
     * @Assert\Regex("/^[a-z_]+$/")
     */
    public $route;

}
