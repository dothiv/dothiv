<?php

namespace Dothiv\PremiumConfiguratorBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\ThumbnailConfiguration;
use Dothiv\BaseWebsiteBundle\Service\ImageScalerInterface;
use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Service\AttachmentStoreInterface;
use Dothiv\BusinessBundle\Service\LinkableAttachmentStoreInterface;
use Dothiv\ValueObject\PathValue;
use Dothiv\ValueObject\URLValue;
use PhpOption\None;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\AcceptHeader;
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
     * @var ThumbnailConfiguration[]|ArrayCollection
     */
    private $thumbnailConfigurations;

    /**
     * @var string
     */
    private $domain;

    public function __construct($config, RouterInterface $router, ImageScalerInterface $scaler, $domain)
    {
        $this->config                  = $config;
        $this->router                  = $router;
        $this->scaler                  = $scaler;
        $this->thumbnailConfigurations = new ArrayCollection();
        foreach ($this->config['thumbnails'] as $label => $c) {
            $this->thumbnailConfigurations->add(new ThumbnailConfiguration(
                $label,
                $c['width'],
                $c['height'],
                $c['thumbnail'],
                $c['exact'],
                $c['fillbg']
            ));
        }
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function store(Attachment $attachment, UploadedFile $file)
    {
        $original = $file->move($this->config['location'], sprintf('%s.%s', $attachment->getHandle(), $file->guessExtension()));
        $this->thumbnailConfigurations->map(function (ThumbnailConfiguration $config) use ($original) {
            $thumbnail = PathValue::create($original)->addFilenameSuffix('@' . $config->getLabel());
            $this->scaler->scale($original, $config, $thumbnail->getFileInfo());
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(Attachment $attachment, $accept = null)
    {
        $sizeLabels = $this->thumbnailConfigurations->map(function (ThumbnailConfiguration $c) {
            return $c->getLabel();
        });
        $scale      = $sizeLabels[0];
        // Check if scale request via accept header
        $a = AcceptHeader::fromString($accept);
        foreach ($a->all() as $item) {
            $acceptScale = $item->getAttribute('scale');
            if (Option::fromValue($acceptScale)->isEmpty()) {
                continue;
            }
            if (!$sizeLabels->contains($acceptScale)) {
                continue;
            }
            $scale = $acceptScale;
            break;
        }
        $filename = PathValue::create($attachment->getHandle() . '.' . $attachment->getExtension())
            ->addFilenameSuffix('@' . $scale);
        return new URLValue(
            sprintf(
                '%s://%s%s%s',
                $this->router->getContext()->getScheme(),
                $this->domain,
                $this->config['url_prefix'],
                $filename->getPathname()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve(Attachment $attachment)
    {
        $file = new \SplFileInfo($this->config['location'] . DIRECTORY_SEPARATOR . sprintf('%s.%s', $attachment->getHandle(), $attachment->getExtension()));
        if ($file->isFile()) {
            return Option::fromValue($file);
        } else {
            return None::create();
        }
    }
}
