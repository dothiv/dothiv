<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\LimitExceededException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Service\Clock;
use Dothiv\BusinessBundle\Service\UserService;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\DateTime;

class AccountController
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
     */
    public function loginLinkAction(Request $request)
    {
        try {
            $this->userService->sendLoginLinkForEmail($request->get('email'));
            $response = new Response();
            $response->setStatusCode(201);
            return $response;
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException();
        } catch (TemporarilyUnavailableException $e) {
            $response = new Response();
            $response->setStatusCode(429);
            $response->headers->add(
                array('Retry-After' => $e->getRetryTime()->getTimestamp() - $this->clock->getNow()->getTimestamp())
            );
            return $response;
        }
    }
}
