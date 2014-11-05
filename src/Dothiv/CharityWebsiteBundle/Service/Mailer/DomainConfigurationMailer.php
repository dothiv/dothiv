<?php


namespace Dothiv\CharityWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailer;

class DomainConfigurationMailer extends ContentMailer
{

    /**
     * @var array
     */
    private $data;

    /**
     * {@inheritdoc}
     */
    public function sendContentTemplateMail($code, $locale, $to, $toName, array $data)
    {
        $this->data = $data;
        parent::sendContentTemplateMail($code, $locale, $to, $toName, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function sendMessage(\Swift_Message $message)
    {
        if ($this->data['forward']) {
            $indexTemplate = $this->getContent()->buildEntry('Block', 'iframe.template.email', 'en');
            $indexHtml     = $this->getTwig()->render($indexTemplate->text, $this->data);
            $message->attach(\Swift_Attachment::newInstance($indexHtml, 'index.html', 'text/html'));
        }
        parent::sendMessage($message);
    }
} 
