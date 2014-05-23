<?php

namespace Dothiv\ContentfulBundle\Listener;

use Dothiv\ContentfulBundle\Event\ContentfulAssetEvent;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\Traits\ContentfulItem;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepository;

class SyncAsset
{
    /**
     * @var ContentfulAssetRepository
     */
    private $assetRepo;

    /**
     * @param ContentfulAssetRepository $assetRepo
     */
    public function __construct(ContentfulAssetRepository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
    }

    /**
     * @param ContentfulAssetEvent $event
     */
    public function onAssetSync(ContentfulAssetEvent $event)
    {
        $syncAsset     = $event->getAsset();
        $assetOptional = $this->assetRepo->findNewestById($syncAsset->getSpaceId(), $syncAsset->getId());
        if ($assetOptional->isEmpty()) {
            $this->assetRepo->persist($syncAsset);
        } else {
            /** @var ContentfulAsset $existingAsset */
            $existingAsset = $assetOptional->get();
            if ($existingAsset->getRevision() < $syncAsset->getRevision()) {
                $this->assetRepo->persist($syncAsset);
            } else {
                $event->setAsset($existingAsset);
            }
        }
    }
}
