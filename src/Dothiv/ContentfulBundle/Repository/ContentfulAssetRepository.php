<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use PhpOption\Option;

interface ContentfulAssetRepository
{
    /**
     * @param string $id
     *
     * @return Option
     */
    function findNewestById($id);

    /**
     * @param ContentfulAsset $asset
     *
     * @return void
     */
    function persist(ContentfulAsset $asset);

    /**
     * @return ContentfulAsset[]|ArrayCollection
     */
    function findAll();
} 
