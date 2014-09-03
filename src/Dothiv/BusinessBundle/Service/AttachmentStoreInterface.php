<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Attachment;
use PhpOption\Option;
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

    /**
     * Retrieve the file for the attachment.
     *
     * @param Attachment $attachment
     *
     * @return Option of \SplFileInfo
     */
    public function retrieve(Attachment $attachment);
} 
