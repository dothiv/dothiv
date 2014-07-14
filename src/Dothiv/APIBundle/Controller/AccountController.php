<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\BusinessBundle\Service\Clock;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dothiv\APIBundle\Annotation\ApiRequest;

class AccountController extends BaseController
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var \Dothiv\BusinessBundle\Service\Clock
     */
    private $clock;

    public function __construct(
        UserServiceInterface $userService,
        Clock $clock
    )
    {
        $this->userService = $userService;
        $this->clock       = $clock;
    }

    /**
     * Generates a login link.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If user is not found.
     *
     * @ApiRequest("Dothiv\APIBundle\Request\LoginLinkRequest")
     */
    public function loginLinkAction(Request $request)
    {
        try {
            $this->userService->sendLoginLinkForEmail($request->attributes->get('model')->email);
            $response = $this->createResponse();
            $response->setStatusCode(201);
            return $response;
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException();
        } catch (TemporarilyUnavailableException $e) {
            $response = $this->createResponse();
            $response->setStatusCode(429);
            $response->headers->add(
                array('Retry-After' => $e->getRetryTime()->getTimestamp() - $this->clock->getNow()->getTimestamp())
            );
            return $response;
        }
    }
}
