<?php

namespace Dothiv\APIBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Generic model used where no specific model is set
 */
class DefaultUpdateRequest implements DataModelInterface
{
    /**
     * {@inheritdoc}
     */
    public function setNonExistingProperties()
    {
        return true;
    }
}
