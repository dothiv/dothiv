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
        if ($view->cfMeta['contentType'] != $this->contentType) return;
        $field = $this->field;
        if (!property_exists($view, $field)) return;
        if (is_array($view->$field)) {
            foreach ($view->$field as $k => $v) {
                $view->{$field}[$k]->file['thumbnails'] = $this->generateThumbnailUrls($v->file['url']);
            }
        } else {
            $view->$field->file['thumbnails'] = $this->generateThumbnailUrls($view->$field->file['url']);
        }
        $event->setView($view);
    }

    protected function generateThumbnailUrls($url)
    {
        $thumbnails = array();
        foreach ($this->scaler->getSizes() as $size) {
            $thumbnails[$size->getLabel()] = $this->scaler->getScaledUrl($url, $size);
        }
        return $thumbnails;
    }
}
