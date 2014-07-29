<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Attachment;

interface URLStoreInterface
{
    /**
     * @param Attachment $attachment
     *
     * @return string
     */
    public function getUrl(Attachment $attachment);
}
