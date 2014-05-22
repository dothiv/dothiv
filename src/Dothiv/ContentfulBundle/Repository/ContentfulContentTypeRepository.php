<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
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
     * Finds ContentTypes by their name. As the name is not unique for content types, multiple entries may be returned.
     *
     * @param string $name
     *
     * @return ContentfulContentType[]|ArrayCollection
     */
    function findNewestByName($name);

    /**
     * @param ContentfulContentType $contentType
     *
     * @return void
     */
    function persist(ContentfulContentType $contentType);
} 
