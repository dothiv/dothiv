<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\SecurityContext;

class UserController
{
    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        SecurityContext $securityContext,
        DomainRepositoryInterface $domainRepo,
        Serializer $serializer
    )
    {
        $this->domainRepo      = $domainRepo;
        $this->securityContext = $securityContext;
        $this->serializer      = $serializer;
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
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setStatusCode(200);
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
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setStatusCode(200);
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
}
