<?php

namespace Dothiv\PremiumConfiguratorBundle\Service;

use Dothiv\BaseWebsiteBundle\Service\ThumbnailConfiguration;
use Dothiv\BaseWebsiteBundle\Service\ImageScalerInterface;
use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Service\AttachmentStoreInterface;
use Dothiv\BusinessBundle\Service\LinkableAttachmentStoreInterface;
use Dothiv\BusinessBundle\ValueObject\PathValue;
use Dothiv\BusinessBundle\ValueObject\URLValue;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

class AttachmentStoreService implements AttachmentStoreInterface, LinkableAttachmentStoreInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ImageScalerInterface
     */
    private $scaler;

    /**
     * @var ThumbnailConfiguration
     */
    private $thumbnailConfiguration;

    public function __construct($config, RouterInterface $router, ImageScalerInterface $scaler)
    {
        $this->config                 = $config;
        $this->router                 = $router;
        $this->scaler                 = $scaler;
        $this->thumbnailConfiguration = new ThumbnailConfiguration(
            'small',
            $this->config['thumbnail']['width'],
            $this->config['thumbnail']['height'],
            $this->config['thumbnail']['thumbnail'],
            $this->config['thumbnail']['exact'],
            $this->config['thumbnail']['fillbg']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function store(Attachment $attachment, UploadedFile $file)
    {
        $original  = $file->move($this->config['location'], sprintf('%s.%s', $attachment->getHandle(), $file->guessExtension()));
        $thumbnail = PathValue::create($original)->addFilenameSuffix('@' . $this->thumbnailConfiguration->getLabel());
        $this->scaler->scale($original, $this->thumbnailConfiguration, $thumbnail->getFileInfo());
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(Attachment $attachment)
    {
        $filename = PathValue::create($attachment->getHandle() . '.' . $attachment->getExtension())
            ->addFilenameSuffix('@' . $this->thumbnailConfiguration->getLabel());
        return new URLValue(
            sprintf(
                '%s://%s%s%s',
                $this->router->getContext()->getScheme(),
                $this->router->getContext()->getHost(),
                $this->config['url_prefix'],
                $filename->getPathname()
            )
        );
    }
}
