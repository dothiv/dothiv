<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Service\AttachmentServiceInterface;
use JMS\Serializer\Serializer;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\SecurityContext;

class AttachmentController
{
    use CreateJsonResponseTrait;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var AttachmentServiceInterface
     */
    private $attachmentService;

    public function __construct(
        AttachmentServiceInterface $attachmentService,
        SecurityContext $securityContext,
        Serializer $serializer)
    {
        $this->attachmentService = $attachmentService;
        $this->securityContext   = $securityContext;
        $this->serializer        = $serializer;
    }

    /**
     * Creates a new attachment
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     * @throws AccessDeniedHttpException
     */
    public function createAction(Request $request)
    {
        /* @var User $user */
        $user = $this->securityContext->getToken()->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }
        if ($request->files->count() != 1) {
            throw new BadRequestHttpException('No file provided.');
        }

        /* @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->getIterator()->current();
        try {
            $this->attachmentService->validateUpload($file);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        $attachment = $this->attachmentService->createAttachment($user, $file);
        $response   = $this->createResponse();
        $response->setContent($this->serializer->serialize($attachment, 'json'));
        $response->setStatusCode(201);
        if (strpos($request->headers->get('User-Agent'), 'MSIE 9.0') !== false) {
            // IE 9 uploader cannot handle json responses
            $response->headers->set('Content-Type', 'text/html; charset=utf-8');
            $response->setContent($attachment->getHandle());
        }
        $location = $this->attachmentService->getUrl($attachment);
        if ($location->isDefined()) {
            $response->headers->set('Location', (string)$location->get());
        }
        return $response;
    }
}
