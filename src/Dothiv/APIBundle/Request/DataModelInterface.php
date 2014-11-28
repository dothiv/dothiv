<?php

namespace Dothiv\APIBundle\Request;

/**
 * Interface for all requests
 */
interface DataModelInterface
{
    /**
     * Called by the {@link ViewRequestListener} to determine if properties should be set regardless of their existence
     *
     * @return bool
     */
    public function setNonExistingProperties();
}
