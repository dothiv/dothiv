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
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $field;

    /**
     * @param ImageScaler $scaler
     * @param string      $contentType
     * @param string      $field
     */
    public function __construct(ImageScaler $scaler, $contentType, $field)
    {
        $this->scaler      = $scaler;
        $this->contentType = $contentType;
        $this->field       = $field;
    }

    /**
     * @param ContentfulViewEvent $event
     */
    public function onViewCreate(ContentfulViewEvent $event)
    {
        $view = $event->getView();
        // TODO: make this work for other content types, too.
        if ($view->cfMeta['contentType'] != $this->contentType) return;
        $field = $this->field;
        if (!property_exists($view, $field)) return;
        $view->$field->file['thumbnails'] = array();
        foreach ($this->scaler->getSizes() as $size) {
            $view->$field->file['thumbnails'][$size->getLabel()] = $this->scaler->getScaledUrl($view->$field->file['url'], $size);
        }
        $event->setView($view);
    }
}
