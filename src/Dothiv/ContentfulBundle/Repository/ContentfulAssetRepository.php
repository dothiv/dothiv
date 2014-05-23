<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use PhpOption\Option;

interface ContentfulAssetRepository
{
    /**
     * @param string $spaceId
     * @param string $id
     *
     * @return Option
     */
    function findNewestById($spaceId, $id);

    /**
     * @param ContentfulAsset $asset
     *
     * @return void
     */
    function persist(ContentfulAsset $asset);

    /**
     * @param string $spaceId
     *
     * @return ContentfulAsset[]|ArrayCollection
     */
    function findAllBySpaceId($spaceId);

    /**
     * @return ContentfulAsset[]|ArrayCollection
     */
    function findAll();
} 
