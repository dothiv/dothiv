<?php

namespace Dothiv\APIBundle\JsonLd\Client;

use Dothiv\APIBundle\Exception\ServiceException;
use Dothiv\APIBundle\JsonLd\JsonLdEntity;
use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdTypedEntity;
use Dothiv\APIBundle\Model\EntryPointModel;
use Dothiv\APIBundle\Model\PaginatedList;
use Dothiv\ValueObject\URLValue;
use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

/**
 * Wraps a Guzzle client and adds JSON-LD aware methods.
 */
class JsonLdGuzzleClient
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param URLValue $url
     * @param string   $itemClass
     *
     * @return PaginatedList
     */
    public function getListResponse(URLValue $url, $itemClass)
    {
        $response = $this->get($url);
        $data     = $this->getData($response);
        $this->expectProperty($data, '@context', 'http://jsonld.click4life.hiv/List');
        $context = $id = constant("$itemClass::CONTEXT");
        $this->expectProperty($data, '@type', $context);
        $list = new PaginatedList();
        $list->setTotal($data->total);
        foreach ($data->items as $item) {
            /** @var JsonLdEntityInterface $instance */
            $instance = new $itemClass();
            $data     = (array)$item;
            foreach (get_class_vars($itemClass) as $k => $v) {
                $instance->$k = $data[$k];
            }
            if (property_exists($item, '@id') && $instance instanceof JsonLdEntityInterface) {
                $instance->setJsonLdId(new URLValue($item->{'@id'}));
            }
            $list->addItem($instance);
        }

        $list->setNextPageUrl($this->getNextUrl($response));
        return $list;
    }

    /**
     * @param URLValue $url
     *
     * @return EntryPointModel
     */
    public function getEntryPointResponse(URLValue $url)
    {
        $response = $this->get($url);
        $data     = $this->getData($response);
        $this->expectProperty($data, '@context', 'http://jsonld.click4life.hiv/EntryPoint');
        $model = new EntryPointModel();
        foreach ($data as $linkInfo) {
            if (!is_object($linkInfo)) {
                continue;
            }
            $id = !preg_match('/^http/', $linkInfo->{'@id'}) ? new URLValue(rtrim($url->toScalar(), '/') . '/' . ltrim($linkInfo->{'@id'}, '/')) : $linkInfo->{'@id'};
            if (property_exists($linkInfo, '@type')) {
                $entity = new JsonLdTypedEntity();
                $entity->setJsonLdType(new URLValue($linkInfo->{'@type'}));
            } else {
                $entity = new JsonLdEntity();
            }
            $entity->setJsonLdContext(new UrlValue($linkInfo->{'@context'}));
            $entity->setJsonLdId($id);
            $model->addLink($id, $entity);
        }
        return $model;
    }

    /**
     * @param \stdClass $object
     * @param string    $property
     * @param string    $expectedValue
     *
     * @return mixed
     * @throws ServiceException
     */
    protected function expectProperty(\stdClass $object, $property, $expectedValue)
    {
        if (!property_exists($object, $property)) {
            throw new ServiceException(
                sprintf(
                    'Missing %s in "%s"', $property, substr(json_encode($object), 0, 25)
                )
            );
        }
        $value = $object->{$property};
        if ($value != $expectedValue) {
            throw new ServiceException(
                sprintf(
                    'Invalid %s "%s", expected "%s" in "%s"', $property, $value, $expectedValue, substr(json_encode($object), 0, 25)
                )
            );
        }
        return $value;
    }

    /**
     * @param Response $response
     *
     * @return URLValue|null
     */
    protected function getNextUrl(Response $response)
    {
        $linkHeader = $response->getHeader('Link');
        if (!$linkHeader) {
            return null;
        }
        if (preg_match('/<([^>]+)>; *rel="next"/', $linkHeader, $nextMatch)) {
            $link = $nextMatch[1];
            if (preg_match('%^(//|https?)%', $link)) {
                return new URLValue($link);
            } else {
                $parts = parse_url($response->getEffectiveUrl());
                return new URLValue(sprintf('%s://%s:%d%s', $parts['scheme'], $parts['host'], $parts['port'], $link));
            }
        }
    }

    /**
     * @param URLValue $url
     *
     * @return Response
     */
    protected function get(URLValue $url)
    {
        $request = $this->client->get((string)$url, array('Accept' => 'application/json'));
        return $this->sendRequest($request);
    }

    /**
     * @param Response $response
     *
     * @return \stdClass|false
     */
    protected function getData(Response $response)
    {
        $data = json_decode($response->getBody(true));
        if (!is_object($data)) {
            throw new ServiceException(
                sprintf(
                    'Unexpected %s, expected object', gettype($data)
                )
            );
        }
        return $data;
    }

    /**
     * Posts to the service.
     *
     * @param URLValue $url
     * @param array    $data
     *
     * @return Response
     */
    public function post(URLValue $url, array $data)
    {
        $request = $this->client->post(
            $url->toScalar(),
            array('Accept' => 'application/json', 'Content-Type' => 'application/json'),
            json_encode($data)
        );
        return $this->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     *
     * @return Response
     *
     * @throws ServiceException
     */
    protected function sendRequest(RequestInterface $request)
    {
        $response = $request->send();
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new ServiceException(
                sprintf(
                    'Request to %s failed: (%d) %s', $request->getUrl(), $response->getStatusCode(), $response->getBody(true)
                )
            );
        }
        if ($response->getContentLength() > 0) {
            if ($response->getContentType() != 'application/json') {
                throw new ServiceException(
                    sprintf(
                        'Unexpected contenttype "%s", expected "application/json"', $response->getContentType()
                    )
                );
            }
        }
        return $response;
    }

    /**
     * Sends a DELETE request to the service.
     *
     * @param URLValue $url
     *
     * @return Response
     */
    public function delete(URLValue $url)
    {
        $request = $this->client->delete(
            $url->toScalar(),
            array('Accept' => 'application/json')
        );
        return $this->sendRequest($request);
    }
}
