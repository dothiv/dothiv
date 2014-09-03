<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface AttachmentServiceInterface
{
    /**
     * @param User         $user
     * @param UploadedFile $file
     *
     * @return Attachment
     */
    public function createAttachment(User $user, UploadedFile $file);

    /**
     * @param UploadedFile $file
     *
     * @throws InvalidArgumentException
     */
    public function validateUpload(UploadedFile $file);

    /**
     * @param Attachment $attachment
     *
     * @return Option of URLValue
     */
    public function getUrl(Attachment $attachment);

    /**
     * @param string $handle
     *
     * @return Option of Attachment
     */
    public function getAttachment($handle);

    /**
     * @param Attachment $attachment
     *
     * @return Option of SplFileInfo
     */
    public function getFile(Attachment $attachment);
}
