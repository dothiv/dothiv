<?php

namespace Dothiv\APIBundle\Request;

class AbstractDataModel implements DataModelInterface
{
    /**
     * {@inheritdoc}
     */
    public function setNonExistingProperties()
    {
        return false;
    }

}
