<?php

namespace Dothiv\BaseWebsiteBundle\Translation;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class ContentfulStringsLoader implements LoaderInterface
{
    /**
     * @var \Dothiv\BaseWebsiteBundle\Contentful\Content
     */
    private $content;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $keyLocale;

    /**
     * @param Content $content
     * @param string  $contentType
     * @param string  $keyLocale
     */
    public function __construct(Content $content, $contentType, $keyLocale)
    {
        $this->content     = $content;
        $this->contentType = $contentType;
        $this->keyLocale   = $keyLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $catalogue = new MessageCatalogue($locale);
        foreach ($this->content->buildEntries($this->contentType, $locale) as $string) {
            $value = isset($string->value) ? $string->value : '';
            $v     = $locale == $this->keyLocale ? $string->code : $value;
            $catalogue->set($string->code, $v, $domain);
        }
        return $catalogue;
    }
}
