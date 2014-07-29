<?php

namespace Dothiv\BusinessBundle\Service;


use Dothiv\BusinessBundle\Entity\Attachment;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface AttachmentStoreInterface
{
    public function store(Attachment $attachment, UploadedFile $file);

} 
