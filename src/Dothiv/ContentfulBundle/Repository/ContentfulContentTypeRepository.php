<?php

namespace Dothiv\ContentfulBundle\Repository;

use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use PhpOption\Option;

interface ContentfulContentTypeRepository
{
    /**
     * @param string $id
     *
     * @return Option
     */
    function findNewestById($id);

    /**
     * @param ContentfulContentType $contentType
     *
     * @return void
     */
    function persist(ContentfulContentType $contentType);
} 
