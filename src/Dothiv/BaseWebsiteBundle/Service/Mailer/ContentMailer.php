<?php

namespace Dothiv\BaseWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\ContentInterface;
use Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapterInterface;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepositoryInterface;

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
     * @var ContentInterface
     */
    private $content;

    /**
     * @var ContentfulAssetAdapterInterface
     */
    private $assetAdapter;

    /**
     * @var ContentfulAssetRepositoryInterface
     */
    private $assetRepo;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param \Swift_Mailer             $mailer
     * @param ContentInterface          $content
     * @param ContentfulAssetAdapterInterface    $assetAdapter
     * @param ContentfulAssetRepositoryInterface $assetRepo
     * @param string                    $emailFromAddress
     * @param string                    $emailFromName
     */
    public function __construct(
        \Swift_Mailer $mailer,
        ContentInterface $content,
        ContentfulAssetAdapterInterface $assetAdapter,
        ContentfulAssetRepositoryInterface $assetRepo,
        $emailFromAddress,
        $emailFromName)
    {
        $this->mailer           = $mailer;
        $this->content          = $content;
        $this->assetAdapter     = $assetAdapter;
        $this->assetRepo        = $assetRepo;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName    = $emailFromName;
        $this->twig             = new \Twig_Environment(new \Twig_Loader_String());
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
        $subject  = $this->twig->render($template->subject, $data);
        $text     = $this->twig->render($template->text, $data);

        // prepare email
        $message = $this->createMessage($to, $toName);
        $message->setSubject($subject);
        $message->setBody($text);

        // Add HTML part.
        if (property_exists($template, 'html')) {
            $html = '';
            if (property_exists($template, 'htmlHead')) {
                $html .= $template->htmlHead;
            }
            $parsedown = new \Parsedown();
            $html .= $this->twig->render($parsedown->text($template->html), $data);
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

        $this->sendMessage($message);
    }

    /**
     * @param $message
     */
    protected function sendMessage(\Swift_Message $message)
    {
        $this->mailer->send($message);
    }

    /**
     * @param $to
     * @param $toName
     *
     * @return \Swift_Message
     */
    protected function createMessage($to, $toName)
    {
        $message = \Swift_Message::newInstance();
        $message
            ->setFrom($this->emailFromAddress, $this->emailFromName)
            ->setTo($to, $toName);
        return $message;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

} 
