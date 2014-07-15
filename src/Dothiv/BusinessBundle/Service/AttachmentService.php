<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Repository\AttachmentRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Util\SecureRandom;

class AttachmentService implements AttachmentServiceInterface
{
    /**
     * @var \Dothiv\BusinessBundle\Repository\AttachmentRepositoryInterface
     */
    private $attachmentRepo;

    /**
     * @var string
     */
    private $attachmentLocation;

    public function __construct(
        AttachmentRepositoryInterface $attachmentRepo,
        $attachmentLocation)
    {
        $this->attachmentRepo     = $attachmentRepo;
        $this->attachmentLocation = $attachmentLocation;
    }

    /**
     * {@inheritdoc}
     */
    public function createAttachment(User $user, UploadedFile $file)
    {
        $attachment = new Attachment();
        $attachment->setUser($user);
        $attachment->setHandle($this->generateHandle());
        $this->attachmentRepo->persist($attachment)->flush();

        $file->move($this->attachmentLocation, sprintf('%s.pdf', $attachment->getHandle()));
        return $attachment;
    }

    /**
     * {@inheritdoc}
     */
    public function validateUpload(UploadedFile $file)
    {
        $mime             = $file->getMimeType();
        $allowedMimeTypes = array('application/pdf');
        if (!in_array($mime, $allowedMimeTypes)) {
            throw new InvalidArgumentException(
                sprintf('Must provide attachment of type %s. %s provied.', join(', ', $allowedMimeTypes), $mime)
            );
        }
    }

    /**
     * @return string
     */
    protected function generateHandle()
    {
        $sr = new SecureRandom();
        return bin2hex($sr->nextBytes(16));
    }
} 
