<?php

namespace Dothiv\BaseWebsiteBundle\Listener\Contentful\ViewCreate;

use Dothiv\BaseWebsiteBundle\Event\ContentfulViewEvent;

class VideoBlockListener
{
    /**
     * @param ContentfulViewEvent $event
     */
    public function onViewCreate(ContentfulViewEvent $event)
    {
        $view = $event->getView();
        if ($view->cfMeta['contentType'] != 'Block') return;
        if (!property_exists($view, 'video')) return;
        $view->video->embed_url = $this->createEmbedUrl($view->video->url);
        $event->setView($view);
    }

    protected function createEmbedUrl($youtubeUrl)
    {
        parse_str(parse_url($youtubeUrl, PHP_URL_QUERY), $q);
        return '//www.youtube.com/embed/' . $q['v'];
    }
} 
