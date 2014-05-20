<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Dothiv\ContentfulBundle\Client\HttpClient;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Dothiv\ContentfulBundle\Item\ContentfulItem;

class HttpClientAdapter implements ContentfulApiAdapter
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @param string     $spaceId
     * @param string     $accessToken
     * @param HttpClient $client
     */
    public function __construct($spaceId, $accessToken, HttpClient $client)
    {
        $this->baseUrl     = sprintf(
            'http://cdn.contentful.com/spaces/%s/',
            urlencode($spaceId)
        );
        $this->accessToken = $accessToken;
        $this->client      = $client;
    }

    /**
     * @param array $filter
     *
     * @return ContentfulEntry[]
     */
    public function queryEntries(array $filter = null)
    {
        $params   = array_merge($filter, array('access_token' => $this->accessToken));
        $url      = $this->baseUrl . 'entries?' . http_build_query($params);
        $response = $this->client->get($url);
        $data     = json_decode($response);
        $items    = array();

        foreach ($data->items as $item) {
            if ($item = $this->getEntry($item, $data->includes)) {
                $items[] = $item;
            }
        }
        return $items;
    }

    protected function getEntry(\stdClass $data, \stdClass &$includes)
    {
        if ($data->sys->type == 'Link') {
            foreach ($includes->{$data->sys->linkType} as $include) {
                if ($include->sys->id == $data->sys->id) {
                    return $this->getEntry($include, $includes);
                }
            }
        }
        switch ($data->sys->type) {
            case 'Entry':
            case 'Asset':
                $entry = new ContentfulItem();
                break;
            default:
                return;
        }

        $entry->sys->createdAt = $data->sys->createdAt;
        $entry->sys->id        = $data->sys->id;
        $entry->sys->updatedAt = $data->sys->updatedAt;
        $entry->sys->locale    = $data->sys->locale;
        $entry->sys->revision  = $data->sys->revision;
        foreach ($data->fields as $k => $field) {
            if (is_array($field)) {
                $entry->fields[$k] = array();
                foreach ($field as $subItem) {
                    $entry->fields[$k][] = $this->getEntry($subItem, $includes);
                }
            } else if (is_object($field) && property_exists($field, 'sys')) {
                $entry->fields[$k] = $this->getEntry($field, $includes);
            } else {
                $entry->fields[$k] = $field;
            }
        }
        return $entry;
    }
}
