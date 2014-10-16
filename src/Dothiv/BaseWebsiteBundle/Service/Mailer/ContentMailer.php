<?php

namespace Dothiv\BaseWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapter;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepository;

class ContentMailer implements ContentMailerInterface
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $emailFromAddress;

    /**
     * @var string
     */
    private $emailFromName;

    /**
     * @var Content
     */
    private $content;

    /**
     * @var ContentfulAssetAdapter
     */
    private $assetAdapter;

    /**
     * @var ContentfulAssetRepository
     */
    private $assetRepo;

    /**
     * @param \Swift_Mailer             $mailer
     * @param Content                   $content
     * @param ContentfulAssetAdapter    $assetAdapter
     * @param ContentfulAssetRepository $assetRepo
     * @param string                    $emailFromAddress
     * @param string                    $emailFromName
     */
    public function __construct(
        \Swift_Mailer $mailer,
        Content $content,
        ContentfulAssetAdapter $assetAdapter,
        ContentfulAssetRepository $assetRepo,
        $emailFromAddress,
        $emailFromName)
    {
        $this->mailer           = $mailer;
        $this->content          = $content;
        $this->assetAdapter     = $assetAdapter;
        $this->assetRepo        = $assetRepo;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName    = $emailFromName;
    }

    /**
     * @param string $code
     * @param string $locale
     * @param string $to
     * @param string $toName
     * @param array  $data
     */
    public function sendContentTemplateMail($code, $locale, $to, $toName, $data)
    {
        $template = $this->content->buildEntry('eMail', $code, $locale);
        $twig     = new \Twig_Environment(new \Twig_Loader_String());
        $subject  = $twig->render($template->subject, $data);
        $text     = $twig->render($template->text, $data);

        // send email
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($subject)
            ->setFrom($this->emailFromAddress, $this->emailFromName)
            ->setTo($to, $toName)
            ->setBody($text);

        // Add HTML part.
        if (property_exists($template, 'html')) {
            $html = '';
            if (property_exists($template, 'htmlHead')) {
                $html .= $template->htmlHead;
            }
            $parsedown = new \Parsedown();
            $html .= $twig->render($parsedown->text($template->html), $data);
            if (property_exists($template, 'htmlFoot')) {
                $html .= $template->htmlFoot;
            }
            $message->addPart($html, 'text/html');
        }

        // Add assets
        if (property_exists($template, 'assets')) {
            foreach ($template->assets as $asset) {
                $assetEntry = $this->assetRepo->findNewestById($this->content->getSpaceId(), $asset->cfMeta['itemId']);
                if ($assetEntry->isDefined()) {
                    $file       = $this->assetAdapter->getLocalFile($assetEntry->get(), $locale);
                    $attachment = \Swift_Attachment::fromPath($file);
                    $filename   = sprintf('%s %s', $asset->title, $asset->description);
                    $filename   = iconv('UTF8', 'ASCII//TRANSLIT', $filename);
                    $filename   = preg_replace('/[^\w]/', '_', $filename);
                    $filename .= '.' . $file->getExtension();
                    $attachment->setFilename($filename);
                    $message->attach($attachment);
                }
            }
        }

        $this->mailer->send($message);
    }
} 
