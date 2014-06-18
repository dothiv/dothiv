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
     * @param string  $contentFuncName
     */
    public function __construct(
        Content $content,
        $contentFuncName = null
    )
    {
        $this->content         = $content;
        $this->contentFuncName = Option::fromValue($contentFuncName)->getOrElse('content');
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction($this->contentFuncName, array($this, 'buildItem'), array('needs_context' => true))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('behaviour', array($this, 'parseBehaviour'))
        );
    }

    public function buildItem(array $ctx, $type, $name = null, $locale = null)
    {
        if ($name === null) {
            return $this->content->buildEntries($type, Option::fromValue($locale)->getOrElse($ctx['locale']));
        }
        return $this->content->buildEntry($type, $name, Option::fromValue($locale)->getOrElse($ctx['locale']));
    }

    /**
     * This filter parses a blocks behaviour configuration string.
     *
     * @param \stdClass $block
     * @param string    $search
     *
     * @return string|boolean
     */
    public function parseBehaviour(\stdClass $block, $search)
    {
        if (!isset($block->behaviour)) {
            return false;
        }
        $behaviours = array();
        foreach (explode(' ', trim($block->behaviour)) as $b) {
            if (strstr($b, ':')) {
                list($name, $prop) = explode(':', $b, 2);
                $behaviours[$name] = $prop;
            } else {
                $behaviours[$b] = true;
            }
        }
        return isset($behaviours[$search]) ? $behaviours[$search] : false;
    }

    public function getName()
    {
        return 'dothiv_basewebsite_contentful_' . $this->contentFuncName;
    }
}
