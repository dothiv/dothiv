<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\APIBundle\Exception\BadRequestHttpException;
use Dothiv\APIBundle\Exception\ConflictHttpException;
use Dothiv\APIBundle\Transformer\EntityTransformerInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\SecurityContext;

class UserController
{
    use CreateJsonResponseTrait;

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @var UserTokenRepositoryInterface
     */
    private $userTokenRepo;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var ClockValue
     */
    private $clock;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var EntityTransformerInterface
     */
    private $userProfileChangeTransformer;

    public function __construct(
        SecurityContext $securityContext,
        DomainRepositoryInterface $domainRepo,
        UserRepositoryInterface $userRepo,
        UserTokenRepositoryInterface $userTokenRepo,
        Serializer $serializer,
        ClockValue $clock,
        UserServiceInterface $userService,
        EntityTransformerInterface $userProfileChangeTransformer
    )
    {
        $this->domainRepo                   = $domainRepo;
        $this->userRepo                     = $userRepo;
        $this->userTokenRepo                = $userTokenRepo;
        $this->securityContext              = $securityContext;
        $this->serializer                   = $serializer;
        $this->clock                        = $clock;
        $this->userService                  = $userService;
        $this->userProfileChangeTransformer = $userProfileChangeTransformer;
    }

    /**
     * Gets this user's domains.
     *
     * @param string $handle
     *
     * @return Response
     */
    public function domainsAction($handle)
    {
        $user     = $this->verifyUserHandle($handle);
        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($user->getDomains(), 'json'));
        return $response;
    }

    /**
     * Returns the users profile.
     *
     * @param string $handle
     *
     * @return Response
     */
    public function profileAction($handle)
    {
        $user     = $this->verifyUserHandle($handle);
        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($user, 'json'));
        return $response;
    }

    /**
     * @param $handle
     *
     * @return User
     * @throws AccessDeniedHttpException If tries to fetch domains for another user.
     */
    protected function verifyUserHandle($handle)
    {
        /* @var User $user */
        $user = $this->securityContext->getToken()->getUser();
        if ($user->getHandle() !== $handle) {
            throw new AccessDeniedHttpException();
        }
        return $user;
    }

    /**
     * Revokes the token for the user.
     *
     * @param $handle
     *
     * @return Response
     */
    public function revokeTokenAction($handle)
    {
        $this->verifyUserHandle($handle);
        $token = $this->userTokenRepo->getTokenByBearerToken($this->securityContext->getToken()->getBearerToken())->get();
        $token->revoke($this->clock->getNow());
        $this->userTokenRepo->persist($token)->flush();
        return $this->createResponse();
    }

    /**
     * Updates a user profile
     *
     * @param Request $request
     * @param string  $handle
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     */
    public function updateProfileAction(Request $request, $handle)
    {
        $user = $this->verifyUserHandle($handle);
        $data = json_decode($request->getContent());
        if (property_exists($data, 'email')) {
            // User wants to change his email address
            $newEmail = new EmailValue($data->email);
            $oldEmail = new EmailValue($user->getEmail());
            if (!$newEmail->equals($oldEmail)) {
                if ($this->userRepo->getUserByEmail($newEmail->toScalar())->isDefined()) {
                    throw new ConflictHttpException(sprintf('Email address already in use: "%s"!', $newEmail));
                }
                // this needs confirmation
                $user->setEmail($data->email);
                $change   = $this->userService->updateUser($user);
                $response = $this->createNoContentResponse();
                $response->setStatusCode(201);
                $changeModel = $this->userProfileChangeTransformer->transform($change);
                $response->setContent($this->serializer->serialize($changeModel, 'json'));
                $response->headers->add(array('Location' => $changeModel->getJsonLdId()->toScalar()));
                return $response;
            }
        }
        throw new BadRequestHttpException();
    }
}
