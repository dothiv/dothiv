<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model for an attachment creation request
 */
class AttachmentRequest
{
    /**
     * @var boolean
     * @Assert\NotNull
     */
    protected $public = false;

    /**
     * @param mixed $public
     */
    public function setPublic($public)
    {
        $this->public = (boolean)$public;
    }

    /**
     * @return mixed
     */
    public function isPublic()
    {
        return $this->public;
    }
}
