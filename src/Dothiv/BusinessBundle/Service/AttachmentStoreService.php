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

class AttachmentStoreService implements AttachmentStoreInterface
{
    /**
     * @var string
     */
    private $storeDir;

    public function __construct($storeDir)
    {
        $this->storeDir = $storeDir;
    }

    /**
     * {@inheritdoc}
     */
    public function store(Attachment $attachment, UploadedFile $file)
    {
        $file->move($this->storeDir, sprintf('%s.%s', $attachment->getHandle(), $file->guessExtension()));
    }
} 
