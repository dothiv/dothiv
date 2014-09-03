<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Repository\AttachmentRepositoryInterface;
use PhpOption\None;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Util\SecureRandom;

class AttachmentService implements AttachmentServiceInterface
{
    /**
     * @var \Dothiv\BusinessBundle\Repository\AttachmentRepositoryInterface
     */
    private $attachmentRepo;

    /**
     * @var AttachmentStoreInterface
     */
    private $store;

    /**
     * @var string[]
     */
    private $allowedMimeTypes;

    public function __construct(
        AttachmentRepositoryInterface $attachmentRepo,
        $allowedMimeTypes,
        AttachmentStoreInterface $store
    )
    {
        $this->attachmentRepo   = $attachmentRepo;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->store            = $store;
    }

    /**
     * {@inheritdoc}
     */
    public function createAttachment(User $user, UploadedFile $file)
    {
        $attachment = new Attachment();
        $attachment->setUser($user);
        $attachment->setHandle($this->generateHandle());
        $attachment->setMimeType($file->getMimeType());
        $attachment->setExtension($file->guessExtension());
        $this->attachmentRepo->persist($attachment)->flush();

        $this->store->store($attachment, $file);
        return $attachment;
    }

    /**
     * {@inheritdoc}
     */
    public function validateUpload(UploadedFile $file)
    {
        $mime = $file->getMimeType();
        if (!in_array($mime, $this->allowedMimeTypes)) {
            throw new InvalidArgumentException(
                sprintf('Must provide attachment of type %s. %s provied.', join(', ', $this->allowedMimeTypes), $mime)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(Attachment $attachment)
    {
        if (!($this->store instanceof LinkableAttachmentStoreInterface)) {
            return None::create();
        }
        return Option::fromValue($this->store->getUrl($attachment));
    }

    /**
     * @return string
     */
    protected function generateHandle()
    {
        $sr = new SecureRandom();
        return bin2hex($sr->nextBytes(16));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachment($handle)
    {
        return $this->attachmentRepo->getAttachmentByHandle($handle);
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(Attachment $attachment)
    {
        return $this->store->retrieve($attachment);
    }
}
