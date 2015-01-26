<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder;

use Dothiv\CharityWebsiteBundle\SendWithUs\TemplateRenderer;
use Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapterInterface;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepositoryInterface;
use Dothiv\ValueObject\EmailValue;

class UserReminderMailer
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TemplateRenderer
     */
    private $renderer;

    /**
     * @var EmailValue
     */
    private $emailFromAddress;

    /**
     * @var string
     */
    private $emailFromName;

    /**
     * @param \Swift_Mailer                      $mailer
     * @param TemplateRenderer                   $renderer
     * @param ContentfulAssetAdapterInterface    $assetAdapter
     * @param ContentfulAssetRepositoryInterface $assetRepo
     * @param string                             $emailFromAddress
     * @param string                             $emailFromName
     */
    public function __construct(
        \Swift_Mailer $mailer,
        TemplateRenderer $renderer,
        ContentfulAssetAdapterInterface $assetAdapter,
        ContentfulAssetRepositoryInterface $assetRepo,
        $emailFromAddress,
        $emailFromName
    )
    {
        $this->mailer           = $mailer;
        $this->renderer         = $renderer;
        $this->emailFromAddress = new EmailValue($emailFromAddress);
        $this->emailFromName    = $emailFromName;
        $this->assetAdapter     = $assetAdapter;
        $this->assetRepo        = $assetRepo;
    }

    /**
     * @param array       $data
     * @param EmailValue  $to
     * @param string      $recipientName
     * @param string      $templateId
     * @param array|null  $attachments
     * @param string      $locale
     */
    public function send(array $data, EmailValue $to, $recipientName, $templateId, array $attachments = null, $locale = 'en')
    {
        $message = \Swift_Message::newInstance();
        $message
            ->setFrom($this->emailFromAddress->toScalar(), $this->emailFromName)
            ->setTo($to->toScalar(), $recipientName);

        $this->renderer->render($message, $data, $templateId);

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $assetEntry = $this->assetRepo->findNewestById($attachment['space'], $attachment['id']);
                if ($assetEntry->isDefined()) {
                    /** @var ContentfulAsset $asset */
                    $asset    = $assetEntry->get();
                    $file     = $this->assetAdapter->getLocalFile($assetEntry->get(), $locale);
                    $a        = \Swift_Attachment::fromPath($file);
                    $filename = $asset->title[$locale];
                    if (!empty($asset->description[$locale])) {
                        $filename .= ' ' . $asset->description[$locale];
                    }
                    $filename = iconv('UTF8', 'ASCII//TRANSLIT', $filename);
                    $filename = preg_replace('/[^\w]/', '_', $filename);
                    $filename .= '.' . $file->getExtension();
                    $a->setFilename($filename);
                    $message->attach($a);
                }
            }
        }

        $this->mailer->send($message);
    }
}
