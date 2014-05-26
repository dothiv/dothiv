<?php

namespace Dothiv\BaseWebsiteBundle\Twig\Extension;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use PhpOption\Option;

class ContentfulTwigExtension extends \Twig_Extension
{
    /**
     * @var \Dothiv\BaseWebsiteBundle\Contentful\Content
     */
    private $content;

    /**
     * @param Content $content
     */
    public function __construct(
        Content $content
    )
    {
        $this->content = $content;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cfBlock', array($this, 'buildBlock'), array('needs_context' => true))
        );
    }

    public function buildBlock(array $ctx, $name, $locale = null)
    {
        return $this->content->buildEntry('Block', $name, Option::fromValue($locale)->getOrElse($ctx['locale']));
    }

    public function getName()
    {
        return 'dothiv_basewebsite_contentful';
    }
}
