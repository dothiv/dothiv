<?php


namespace Dothiv\LandingpageBundle\Listener;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\ValueObject\ClockValue;
use PhpOption\Option;

/**
 * This is a perfect use case for an async queue. Ideally this listener should
 * directly ask the click-counter config service to update the click-counter affected
 * by the update of the landingpage configuration.
 *
 * This shouldn't be done directly in the user request, so we update the banners updated
 * timestamp, which will then be picked up by the Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand
 *
 * TODO: Use async queue.
 */
class LandingpageConfigChangedListener
{
    /**
     * @param BannerRepositoryInterface $bannerRepo
     */
    public function __construct(BannerRepositoryInterface $bannerRepo, ClockValue $clock)
    {
        $this->bannerRepo = $bannerRepo;
        $this->clock      = $clock;
    }

    public function onEntityChanged(EntityChangeEvent $event)
    {
        if (!($event->getEntity() instanceof LandingpageConfiguration)) {
            return;
        }
        /** @var LandingpageConfiguration $config */
        $config = $event->getEntity();
        $bannerOptional = Option::fromValue($config->getDomain()->getActiveBanner());
        $clock          = $this->clock;
        $bannerRepo     = $this->bannerRepo;
        $bannerOptional->map(function (Banner $banner) use ($clock, $bannerRepo) {
            $banner->setUpdated($clock->getNow());
            $bannerRepo->persist($banner)->flush();
        });
    }
}
