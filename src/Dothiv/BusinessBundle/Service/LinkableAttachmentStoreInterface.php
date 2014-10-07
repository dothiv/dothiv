<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\ValueObject\URLValue;

interface LinkableAttachmentStoreInterface
{
    /**
     * Generate a hyperlink for the given attachment.
     *
     * @param Attachment $attachment
     * @param string     $accept Accept header expression specifying the accepted url
     *
     * @return URLValue
     */
    public function getUrl(Attachment $attachment, $accept = null);
}
