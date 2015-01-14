<?php


namespace Dothiv\UserReminderBundle\Service;

use Dothiv\CharityWebsiteBundle\Exception\RuntimeException;
use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;

class SendWithUsTemplateRenderer
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param \Swift_Message $message
     * @param array          $data
     * @param string         $templateId
     * @param string         $versionId
     */
    function render(\Swift_Message $message, array $data, $templateId, $versionId)
    {
        $data = [
            'template_id'   => $templateId,
            'version_id'    => $versionId,
            'template_data' => $data
        ];

        $request = $this->getClient()->post(
            'https://api.sendwithus.com/api/v1/render',
            array('Accept' => 'application/json', 'Content-Type' => 'application/json'),
            json_encode($data),
            array('auth' => [$this->apiKey, ''])
        );

        $response = $request->send();
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new RuntimeException(
                sprintf(
                    'Request to %s failed: (%d) %s', $request->getUrl(), $response->getStatusCode(), $response->getBody(true)
                )
            );
        }
        $data = json_decode($response->getBody(true));
        $message->setSubject($data->subject);
        $message->setBody($data->text);
        $message->addPart($data->html, 'text/html');
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client();
        }
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     *
     * @return self
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        return $this;
    }
}
