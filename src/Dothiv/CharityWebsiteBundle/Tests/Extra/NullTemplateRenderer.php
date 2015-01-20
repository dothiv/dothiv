<?php


namespace Dothiv\CharityWebsiteBundle\Tests\Extra;

use Dothiv\CharityWebsiteBundle\SendWithUs\TemplateRenderer;

class NullTemplateRenderer extends TemplateRenderer
{
    /**
     * {@inheritdoc}
     */
    function render(\Swift_Message $message, array $data, $templateId, $versionId)
    {
        // pass.
    }
}
