<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\ValueObject\URLValue;

interface LinkableAttachmentStoreInterface
{
    /**
     * Generate a hyperlink for the given attachment.
     *
     * @param Attachment $attachment
     *
     * @return URLValue
     */
    public function getUrl(Attachment $attachment);
}
