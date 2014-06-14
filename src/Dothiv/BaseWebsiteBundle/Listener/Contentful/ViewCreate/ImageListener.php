<?php

namespace Dothiv\BaseWebsiteBundle\Listener\Contentful\ViewCreate;

use Dothiv\BaseWebsiteBundle\Contentful\ImageScaler;
use Dothiv\BaseWebsiteBundle\Event\ContentfulViewEvent;

class ImageListener
{
    /**
     * @var \Dothiv\BaseWebsiteBundle\Contentful\ImageScaler
     */
    private $scaler;

    /**
     * @param ImageScaler $scaler
     */
    public function __construct(ImageScaler $scaler)
    {
        $this->scaler = $scaler;
    }

    /**
     * @param ContentfulViewEvent $event
     */
    public function onViewCreate(ContentfulViewEvent $event)
    {
        $view = $event->getView();
        if ($view->cfMeta['contentType'] != 'Block') return;
        if (!property_exists($view, 'image')) return;
        $view->image->file['thumbnails'] = array();
        foreach ($this->scaler->getSizes() as $size) {
            $view->image->file['thumbnails'][$size->getLabel()] = $this->scaler->getScaledUrl($view->image->file['url'], $size);
        }
        $event->setView($view);
    }
}
