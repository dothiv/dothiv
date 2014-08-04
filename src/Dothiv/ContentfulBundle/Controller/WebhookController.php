<?php

namespace Dothiv\ContentfulBundle\Controller;

use Dothiv\ContentfulBundle\Adapter\ContentfulContentTypeReader;
use Dothiv\ContentfulBundle\Adapter\ContentfulEntityReader;
use Dothiv\ContentfulBundle\ContentfulEvents;
use Dothiv\ContentfulBundle\Event\ContentfulAssetEvent;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypeEvent;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use Dothiv\ContentfulBundle\Event\DeletedContentfulEntryEvent;
use Dothiv\ContentfulBundle\Logger\LoggerAwareTrait;
use Dothiv\ContentfulBundle\Repository\ContentfulContentTypeRepository;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WebhookController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        ContentfulContentTypeRepository $contentTypeRepo,
        EventDispatcherInterface $dispatcher)
    {
        $this->contentTypeRepo = $contentTypeRepo;
        $this->dispatcher      = $dispatcher;
    }

    public function webhookAction(Request $request)
    {
        $topic = $request->headers->get('X-Contentful-Topic');
        if (empty($topic)) {
            $this->log('Missing X-Contentful-Topic header!');
            throw new BadRequestHttpException(sprintf(
                'Missing X-Contentful-Topic header!'
            ));
        }
        $data    = json_decode($request->getContent());
        if (!is_object($data) || !property_exists($data, 'sys')) {
            throw new BadRequestHttpException('JSON object expected.');
        }
        $id      = $data->sys->id;
        $spaceId = $data->sys->space->sys->id;
        $this->log('Webhook called: type: %s, id: %s', $data->sys->type, $id);

        switch ($topic) {
            case 'ContentManagementAPI.ContentType.publish':
                $reader      = new ContentfulContentTypeReader($spaceId);
                $contentType = $reader->getContentType($data);
                $this->dispatcher->dispatch(
                    ContentfulEvents::CONTENT_TYPE_SYNC,
                    new ContentfulContentTypeEvent($contentType)
                );
                break;
            case 'ContentManagementAPI.ContentType.unpublish':
                // TODO: Implement "ContentManagementAPI.ContentType.unpublish"
                break;
            case 'ContentManagementAPI.Entry.publish':
                $reader = new ContentfulEntityReader($spaceId, $this->contentTypeRepo->findAllBySpaceId($spaceId));
                $entry  = $reader->getEntry($data);
                $this->dispatcher->dispatch(
                    ContentfulEvents::ENTRY_SYNC,
                    new ContentfulEntryEvent($entry)
                );
                break;
            case 'ContentManagementAPI.Entry.unpublish':
                $reader = new ContentfulEntityReader($spaceId, $this->contentTypeRepo->findAllBySpaceId($spaceId));
                $entry  = $reader->getEntry($data);
                $this->dispatcher->dispatch(
                    ContentfulEvents::ENTRY_DELETE,
                    new DeletedContentfulEntryEvent($entry)
                );
                break;
            case 'ContentManagementAPI.Asset.publish':
                $reader = new ContentfulEntityReader($spaceId, $this->contentTypeRepo->findAllBySpaceId($spaceId));
                $entry  = $reader->getEntry($data);
                $this->dispatcher->dispatch(
                    ContentfulEvents::ASSET_SYNC,
                    new ContentfulAssetEvent($entry)
                );
                break;
            case 'ContentManagementAPI.Asset.unpublish':
                // TODO: Implement "ContentManagementAPI.Asset.unpublish"
                break;
        }

        $response = new Response('', 204);
        return $response;
    }
} 
