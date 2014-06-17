<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ContentfulController
{
    /**
     * @var Content
     */
    private $content;

    /**
     * @param Content $content
     */
    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    /**
     * @Template
     */
    public function stringsAction()
    {
        $strings   = array();
        $enStrings = $this->content->buildEntries('String', 'en');
        $deStrings = $this->content->buildEntries('String', 'de');
        $sort      = array();
        foreach ($enStrings as $k => $string) {
            if (mb_substr($string->code, 0, 5) == 'gmbh.') {
                continue;
            }
            $sort[]    = strtolower($string->code);
            $strings[] = array(
                'url'  => $string->cfMeta['url'],
                'code' => $string->code,
                'en'   => property_exists($string, 'value') ? mb_substr($string->value, 0, 100) : null,
                'de'   => property_exists($deStrings[$k], 'value') ? mb_substr($deStrings[$k]->value, 0, 100) : null
            );
        }
        array_multisort($sort, SORT_ASC, $strings);
        $data = array(
            'strings' => $strings
        );

        return $data;
    }
}
