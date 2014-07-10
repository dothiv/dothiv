<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for a banner configuration request
 */
class BannerConfigRequest
{
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Regex("/^[a-z]{2}$/")
     */
    public $language;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Choice({"top","center","right"})
     */
    public $position;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Choice({"top","center","right"})
     */
    public $position_first;

    /**
     * @var string
     * @Assert\Regex("/^(https*:)*\/\/.+/")
     */
    public $redirect_url;
}
