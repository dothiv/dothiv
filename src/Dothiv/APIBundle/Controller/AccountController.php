<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\APIBundle\Request\LoginLinkRequest;
use Dothiv\APIBundle\Request\UserCreateRequest;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Service\Clock;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dothiv\APIBundle\Annotation\ApiRequest;

class AccountController
{
    use CreateJsonResponseTrait;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @var \Dothiv\BusinessBundle\Service\Clock
     */
    private $clock;

    public function __construct(
        UserServiceInterface $userService,
        UserRepositoryInterface $userRepo,
        Serializer $serializer,
        Clock $clock
    )
    {
        $this->userService = $userService;
        $this->userRepo    = $userRepo;
        $this->serializer  = $serializer;
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
            /** @var LoginLinkRequest $model */
            $model = $request->attributes->get('model');
            $this->userService->sendLoginLinkForEmail($model->email, $request->getHttpHost(), $model->locale);
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

    /**
     * Creates a new user
     *
     * @ApiRequest("Dothiv\APIBundle\Request\UserCreateRequest")
     */
    public function createAction(Request $request)
    {
        /* @var UserCreateRequest $model */
        $createRequest = $request->attributes->get('model');
        $optionalUser  = $this->userRepo->getUserByEmail($createRequest->email);
        if ($optionalUser->isDefined()) {
            throw new ConflictHttpException();
        }
        $user = $this->userService->getOrCreateUser($createRequest->email, $createRequest->firstname, $createRequest->surname);
        $this->userService->sendLoginLinkForEmail($user->getEmail(), $request->getHttpHost(), $createRequest->locale);
        $response = $this->createResponse();
        $response->setStatusCode(201);
        $response->setContent($this->serializer->serialize($user, 'json'));
        return $response;
    }
}
