<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Attachment;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface AttachmentStoreInterface
{
    /**
     * Store the file for the attachment.
     *
     * @param Attachment   $attachment
     * @param UploadedFile $file
     *
     * @return void
     */
    public function store(Attachment $attachment, UploadedFile $file);

} 
