<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\Attachment;
use PhpOption\Option;

/**
 * This repository contains the attachments.
 */
interface AttachmentRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param Attachment $attachment
     *
     * @return self
     */
    public function persist(Attachment $attachment);

    /**
     * @param string $handle
     *
     * @return Option
     */
    public function getAttachmentByHandle($handle);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
