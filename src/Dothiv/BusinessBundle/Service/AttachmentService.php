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
     * @var array
     */
    private $config;

    public function __construct(
        AttachmentRepositoryInterface $attachmentRepo,
        $config
    )
    {
        $this->attachmentRepo = $attachmentRepo;
        $this->config         = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function createAttachment(User $user, UploadedFile $file, $public = false)
    {
        $attachment = new Attachment();
        $attachment->setUser($user);
        $attachment->setHandle($this->generateHandle());
        $attachment->setMimeType($file->getMimeType());
        $attachment->setExtension($file->guessExtension());
        $attachment->setPublic($public);
        $this->attachmentRepo->persist($attachment)->flush();

        $dir = $public ? $this->config['public']['location'] : $this->config['private']['location'];
        $file->move($dir, sprintf('%s.%s', $attachment->getHandle(), $file->guessExtension()));
        return $attachment;
    }

    /**
     * {@inheritdoc}
     */
    public function validateUpload(UploadedFile $file)
    {
        $mime             = $file->getMimeType();
        $allowedMimeTypes = array(
            'application/pdf',
            'image/png',
            'image/jpeg',
            'image/gif'
        );
        if (!in_array($mime, $allowedMimeTypes)) {
            throw new InvalidArgumentException(
                sprintf('Must provide attachment of type %s. %s provied.', join(', ', $allowedMimeTypes), $mime)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicUrl(Attachment $attachment)
    {
        if (!$attachment->isPublic()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Attachment "%s" is not public.',
                    $attachment->getHandle()
                )
            );
        }
        return $this->config['public']['prefix'] . '/' . $attachment->getHandle() . '.' . $attachment->getExtension();
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
